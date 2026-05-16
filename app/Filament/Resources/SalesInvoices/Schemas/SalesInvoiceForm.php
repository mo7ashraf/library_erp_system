<?php

namespace App\Filament\Resources\SalesInvoices\Schemas;

use App\Models\SalesInvoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalesInvoiceForm
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
                            ->default(fn (): string => 'SAL-' . now()->format('Ymd-His')),

                        DatePicker::make('invoice_date')
                            ->label('تاريخ الفاتورة')
                            ->required()
                            ->default(now()),

                        DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->nullable(),

                        Select::make('customer_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
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
                                SalesInvoice::PAYMENT_CASH => 'نقدي',
                                SalesInvoice::PAYMENT_CREDIT => 'آجل',
                                SalesInvoice::PAYMENT_PARTIAL => 'جزء نقدي / آجل',
                            ])
                            ->default(SalesInvoice::PAYMENT_CASH)
                            ->required(),

                        Select::make('price_type')
                            ->label('نوع السعر')
                            ->options([
                                SalesInvoice::PRICE_STUDENT => 'سعر طالب',
                                SalesInvoice::PRICE_TEACHER => 'سعر مدرس',
                                SalesInvoice::PRICE_REPRESENTATIVE => 'سعر مندوب',
                                SalesInvoice::PRICE_RETAIL => 'سعر قطاعي',
                                SalesInvoice::PRICE_WHOLESALE => 'سعر جملة',
                            ])
                            ->default(SalesInvoice::PRICE_STUDENT)
                            ->required(),

                        TextInput::make('discount_amount')
                            ->label('خصم على الفاتورة')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('service_amount')
                            ->label('خدمات')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        TextInput::make('commission_percent')
                            ->label('عمولة %')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),

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
                                    ->minValue(0.001),

                                TextInput::make('unit_price')
                                    ->label('سعر البيع')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->prefix('ج.م'),

                                TextInput::make('discount_percent')
                                    ->label('خصم %')
                                    ->numeric()
                                    ->default(0)
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