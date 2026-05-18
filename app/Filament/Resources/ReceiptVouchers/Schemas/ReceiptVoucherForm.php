<?php

namespace App\Filament\Resources\ReceiptVouchers\Schemas;

use App\Models\ReceiptVoucher;
use App\Models\TreasuryTransaction;
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

                        Select::make('party_type')
                            ->label('نوع الطرف')
                            ->options([
                                ReceiptVoucher::PARTY_CUSTOMER => 'عميل',
                                ReceiptVoucher::PARTY_SUPPLIER => 'مورد',
                                ReceiptVoucher::PARTY_OTHER => 'أخرى',
                            ])
                            ->default(ReceiptVoucher::PARTY_CUSTOMER)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('customer_id', null);
                                $set('supplier_id', null);
                                $set('party_name', null);
                            }),

                        Select::make('customer_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('party_type') === ReceiptVoucher::PARTY_CUSTOMER)
                            ->visible(fn (Get $get): bool => $get('party_type') === ReceiptVoucher::PARTY_CUSTOMER),

                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => $get('party_type') === ReceiptVoucher::PARTY_SUPPLIER)
                            ->visible(fn (Get $get): bool => $get('party_type') === ReceiptVoucher::PARTY_SUPPLIER),

                        TextInput::make('party_name')
                            ->label('اسم الطرف')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => $get('party_type') === ReceiptVoucher::PARTY_OTHER)
                            ->visible(fn (Get $get): bool => $get('party_type') === ReceiptVoucher::PARTY_OTHER),

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