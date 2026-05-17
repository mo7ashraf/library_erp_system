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
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesReturnItem;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Hidden;

class SalesReturnForm
{
    private static function availableReturnQuantity(?int $invoiceId, ?int $itemId): float
    {
        if (! $invoiceId || ! $itemId) {
            return 0;
        }

        $originalQty = (float) SalesInvoiceItem::query()
            ->where('sales_invoice_id', $invoiceId)
            ->where('item_id', $itemId)
            ->sum('quantity');

        $returnedQty = (float) SalesReturnItem::query()
            ->where('item_id', $itemId)
            ->whereHas('salesReturn', function ($query) use ($invoiceId) {
                $query
                    ->where('sales_invoice_id', $invoiceId)
                    ->where('status', SalesReturn::STATUS_POSTED);
            })
            ->sum('quantity');

        return max(0, $originalQty - $returnedQty);
    }

    private static function firstInvoiceLine(?int $invoiceId, ?int $itemId): ?SalesInvoiceItem
    {
        if (! $invoiceId || ! $itemId) {
            return null;
        }

        return SalesInvoiceItem::query()
            ->where('sales_invoice_id', $invoiceId)
            ->where('item_id', $itemId)
            ->first();
    }

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
                            ->options(
                                fn () => SalesInvoice::query()
                                    ->where('status', SalesInvoice::STATUS_POSTED)
                                    ->latest('invoice_date')
                                    ->pluck('invoice_number', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $invoice = SalesInvoice::find($state);

                                $set('customer_id', $invoice?->customer_id);
                                $set('warehouse_id', $invoice?->warehouse_id);
                                $set('items', []);
                            }),

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
                                Hidden::make('sales_invoice_item_id'),
                                Select::make('item_id')
                                    ->label('الصنف')
                                    ->options(function (Get $get): array {
                                        $invoiceId = $get('../../sales_invoice_id');

                                        if (! $invoiceId) {
                                            return [];
                                        }

                                        return SalesInvoiceItem::query()
                                            ->with('item')
                                            ->where('sales_invoice_id', $invoiceId)
                                            ->get()
                                            ->groupBy('item_id')
                                            ->mapWithKeys(function ($lines) use ($invoiceId) {
                                                $line = $lines->first();
                                                $availableQty = self::availableReturnQuantity($invoiceId, $line->item_id);

                                                if ($availableQty <= 0) {
                                                    return [];
                                                }

                                                return [
                                                    $line->item_id => ($line->item?->name ?? 'صنف غير معروف')
                                                        . ' — الحد الأقصى للمرتجع: '
                                                        . number_format($availableQty, 3),
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, Get $get, Set $set): void {
                                        $invoiceId = $get('../../sales_invoice_id');
                                        $line = self::firstInvoiceLine($invoiceId, $state);

                                        $set('sales_invoice_item_id', $line?->id);
                                        $set('unit_id', $line?->unit_id);
                                        $set('unit_price', $line?->unit_price ?? 0);
                                        $set('discount_percent', $line?->discount_percent ?? 0);
                                        $set('quantity', null);
                                    })
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
                                    ->minValue(0.001)
                                    ->maxValue(fn (Get $get): float => self::availableReturnQuantity(
                                        $get('../../sales_invoice_id'),
                                        $get('item_id'),
                                    ))
                                    ->helperText(fn (Get $get): string => 'الحد الأقصى المسموح: ' . number_format(
                                        self::availableReturnQuantity(
                                            $get('../../sales_invoice_id'),
                                            $get('item_id'),
                                        ),
                                        3
                                    ))
                                    ->validationAttribute('الكمية المرتجعة'),

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