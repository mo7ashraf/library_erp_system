<?php

namespace App\Filament\Resources\PaymentVouchers\Schemas;

use App\Models\FinanceCategory;
use App\Models\PaymentVoucher;
use App\Models\TreasuryTransaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PaymentVoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات سند الصرف')
                    ->columns(3)
                    ->schema([
                        TextInput::make('voucher_number')
                            ->label('رقم السند')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'PAY-' . now()->format('Ymd-His')),

                        DatePicker::make('voucher_date')
                            ->label('تاريخ السند')
                            ->required()
                            ->default(now()),

                        Select::make('voucher_type')
                            ->label('نوع سند الصرف')
                            ->options([
                                PaymentVoucher::TYPE_SUPPLIER_PAYMENT => 'دفعة لمورد',
                                PaymentVoucher::TYPE_CUSTOMER_REFUND => 'رد مبلغ لعميل',
                                PaymentVoucher::TYPE_GENERAL_EXPENSE => 'مصروف عام',
                                PaymentVoucher::TYPE_OTHER => 'أخرى',
                            ])
                            ->default(PaymentVoucher::TYPE_SUPPLIER_PAYMENT)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('customer_id', null);
                                $set('supplier_id', null);
                                $set('finance_category_id', null);
                                $set('party_name', null);
                            }),

                        Select::make('payment_channel')
                            ->label('طريقة الصرف')
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

                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_SUPPLIER_PAYMENT)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_SUPPLIER_PAYMENT),

                        Select::make('customer_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_CUSTOMER_REFUND)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_CUSTOMER_REFUND),

                        Select::make('finance_category_id')
                            ->label('بند المصروف')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query
                                    ->where('type', FinanceCategory::TYPE_EXPENSE)
                                    ->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_GENERAL_EXPENSE)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_GENERAL_EXPENSE),

                        TextInput::make('party_name')
                            ->label('اسم الطرف')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_OTHER)
                            ->visible(fn (Get $get): bool => $get('voucher_type') === PaymentVoucher::TYPE_OTHER),

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