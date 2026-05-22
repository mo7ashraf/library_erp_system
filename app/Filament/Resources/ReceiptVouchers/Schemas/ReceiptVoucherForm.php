<?php

namespace App\Filament\Resources\ReceiptVouchers\Schemas;

use App\Models\FinanceCategory;
use App\Models\ReceiptVoucher;
use App\Models\TreasuryTransaction;
use App\Services\Finance\PartyLedgerService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ReceiptVoucherForm
{
    private static function customerBalanceLabel(?int $customerId): string
    {
        if (! $customerId) {
            return 'اختر العميل لعرض الرصيد.';
        }

        try {
            $ledger = app(PartyLedgerService::class)->customerLedger($customerId);

            return 'الرصيد الحالي: ' . ($ledger['closing_balance_label'] ?? '0.00');
        } catch (\Throwable) {
            return 'تعذر تحميل رصيد العميل.';
        }
    }

    private static function supplierBalanceLabel(?int $supplierId): string
    {
        if (! $supplierId) {
            return 'اختر المورد لعرض الرصيد.';
        }

        try {
            $ledger = app(PartyLedgerService::class)->supplierLedger($supplierId);

            return 'الرصيد الحالي: ' . ($ledger['closing_balance_label'] ?? '0.00');
        } catch (\Throwable) {
            return 'تعذر تحميل رصيد المورد.';
        }
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات سند القبض')
                    ->columns(3)
                    ->schema([
                        TextInput::make('voucher_number')
                            ->label('رقم السند')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'RCV-' . now()->format('Ymd-His')),

                        DatePicker::make('voucher_date')
                            ->label('تاريخ السند')
                            ->required()
                            ->default(now()),

                        Select::make('voucher_type')
                            ->label('نوع سند القبض')
                            ->options([
                                ReceiptVoucher::TYPE_CUSTOMER_COLLECTION => 'تحصيل من عميل',
                                ReceiptVoucher::TYPE_SUPPLIER_REFUND => 'استرداد من مورد',
                                ReceiptVoucher::TYPE_GENERAL_INCOME => 'إيراد عام',
                                ReceiptVoucher::TYPE_OTHER => 'أخرى',
                            ])
                            ->default(ReceiptVoucher::TYPE_CUSTOMER_COLLECTION)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('customer_id', null);
                                $set('supplier_id', null);
                                $set('finance_category_id', null);
                                $set('party_name', null);
                            }),

                        Select::make('payment_channel')
                            ->label('طريقة التحصيل')
                            ->options([
                                TreasuryTransaction::CHANNEL_CASH => 'خزينة',
                                TreasuryTransaction::CHANNEL_BANK => 'بنك',
                            ])
                            ->default(TreasuryTransaction::CHANNEL_CASH)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('cashbox_id', null);
                                $set('bank_account_id', null);
                            }),

                        Select::make('cashbox_id')
                            ->label('الخزينة')
                            ->relationship('cashbox', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('payment_channel') === TreasuryTransaction::CHANNEL_CASH)
                            ->visible(fn (Get $get): bool => $get('payment_channel') === TreasuryTransaction::CHANNEL_CASH),

                        Select::make('bank_account_id')
                            ->label('الحساب البنكي')
                            ->relationship('bankAccount', 'account_name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('payment_channel') === TreasuryTransaction::CHANNEL_BANK)
                            ->visible(fn (Get $get): bool => $get('payment_channel') === TreasuryTransaction::CHANNEL_BANK),

                        Select::make('customer_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_CUSTOMER_COLLECTION)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_CUSTOMER_COLLECTION)
                            ->helperText(fn (Get $get): string => self::customerBalanceLabel($get('customer_id'))),
                            
                       Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_SUPPLIER_REFUND)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_SUPPLIER_REFUND)
                            ->helperText(fn (Get $get): string => self::supplierBalanceLabel($get('supplier_id'))),

                        Select::make('finance_category_id')
                            ->label('بند الإيراد')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query
                                    ->where('type', FinanceCategory::TYPE_INCOME)
                                    ->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_GENERAL_INCOME)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_GENERAL_INCOME),

                        TextInput::make('party_name')
                            ->label('اسم الطرف')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_OTHER)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === ReceiptVoucher::TYPE_OTHER),

                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->prefix('ج.م'),

                        Textarea::make('description')
                            ->label('البيان')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}