<?php

namespace App\Filament\Resources\SalesReturns\Schemas;

use App\Models\SalesReturn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalesReturnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات المرتجع')
                    ->columns(3)
                    ->schema([
                        TextInput::make('return_number')
                            ->label('رقم المرتجع')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'SRET-' . now()->format('Ymd-His')),

                        DatePicker::make('return_date')
                            ->label('تاريخ المرتجع')
                            ->required()
                            ->default(now()),

                        Select::make('sales_invoice_id')
                            ->label('فاتورة البيع الأصلية')
                            ->relationship('salesInvoice', 'invoice_number')
                            ->searchable()
                            ->preload()
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

                        Select::make('refund_type')
                            ->label('طريقة رد القيمة')
                            ->options([
                                SalesReturn::REFUND_CASH => 'رد نقدي',
                                SalesReturn::REFUND_CREDIT_BALANCE => 'إضافة إلى رصيد العميل',
                            ])
                            ->default(SalesReturn::REFUND_CASH)
                            ->required(),

                        TextInput::make('discount_amount')
                            ->label('خصم على المرتجع')
                            ->numeric()
                            ->default(0)
                            ->prefix('ج.م'),

                        Textarea::make('notes')
                            ->label('بيان / ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('أصناف المرتجع')
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
                                    ->label('الكمية المرتجعة')
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