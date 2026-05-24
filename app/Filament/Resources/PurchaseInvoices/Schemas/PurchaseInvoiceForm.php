<?php

namespace App\Filament\Resources\PurchaseInvoices\Schemas;

use App\Models\Cashbox;
use App\Models\PurchaseInvoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PurchaseInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات الفاتورة')
                    ->columns(3)
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('رقم الفاتورة')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'PUR-' . now()->format('Ymd-His')),

                        TextInput::make('supplier_invoice_number')
                            ->label('رقم فاتورة المورد')
                            ->maxLength(100),

                        DatePicker::make('invoice_date')
                            ->label('تاريخ الفاتورة')
                            ->required()
                            ->default(now()),

                        DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->nullable(),

                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('warehouse_id')
                            ->label('المخزن')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('payment_type')
                            ->label('طريقة السداد')
                            ->options([
                                PurchaseInvoice::PAYMENT_CASH => 'نقدي',
                                PurchaseInvoice::PAYMENT_CREDIT => 'آجل',
                                PurchaseInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                            ])
                            ->default(PurchaseInvoice::PAYMENT_CASH)
                            ->live()
                            ->required(),

                        Select::make('cashbox_id')
                            ->label('الخزينة')
                            ->options(fn (): array => Cashbox::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(fn (Get $get): bool => $get('payment_type') !== PurchaseInvoice::PAYMENT_CREDIT)
                            ->visible(fn (Get $get): bool => $get('payment_type') !== PurchaseInvoice::PAYMENT_CREDIT)
                            ->dehydrated(fn (Get $get): bool => $get('payment_type') !== PurchaseInvoice::PAYMENT_CREDIT)
                            ->helperText('تستخدم لإنشاء سند صرف تلقائي عند الدفع الكامل أو الجزئي.'),

                        TextInput::make('paid_amount')
                            ->label('المبلغ المدفوع')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->visible(fn (Get $get): bool => $get('payment_type') === PurchaseInvoice::PAYMENT_PARTIAL)
                            ->required(fn (Get $get): bool => $get('payment_type') === PurchaseInvoice::PAYMENT_PARTIAL)
                            ->dehydrated(fn (Get $get): bool => $get('payment_type') === PurchaseInvoice::PAYMENT_PARTIAL)
                            ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
                            ->prefix('ج.م')
                            ->helperText('في الدفع الجزئي يجب أن يكون المبلغ أكبر من صفر وأقل من إجمالي الفاتورة.'),

                        TextInput::make('discount_amount')
                            ->label('خصم على الفاتورة')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
                            ->prefix('ج.م'),

                        TextInput::make('additional_cost')
                            ->label('مصروفات إضافية')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
                            ->prefix('ج.م'),

                        Textarea::make('notes')
                            ->label('بيان / ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('أصناف الفاتورة')
                    ->schema([
                        Repeater::make('items')
                            ->label('الأصناف')
                            ->relationship('items')
                            ->columns(6)
                            ->schema([
                                Select::make('item_id')
                                    ->label('الصنف')
                                    ->relationship('item', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),

                                Select::make('unit_id')
                                    ->label('الوحدة')
                                    ->relationship('unit', 'name')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),

                                TextInput::make('unit_price')
                                    ->label('سعر الشراء')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.01)
                                    ->validationMessages([
                                        'min' => 'سعر الشراء يجب أن يكون أكبر من صفر.',
                                    ])
                                    ->prefix('ج.م'),

                                TextInput::make('discount_percent')
                                    ->label('خصم %')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
                                    ->suffix('%'),

                                TextInput::make('notes')
                                    ->label('ملاحظات')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('إضافة صنف')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}