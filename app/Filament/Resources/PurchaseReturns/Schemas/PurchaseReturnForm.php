<?php

namespace App\Filament\Resources\PurchaseReturns\Schemas;

use App\Models\PurchaseReturn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseReturnItem;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class PurchaseReturnForm
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
                            ->default(fn (): string => 'PRET-' . now()->format('Ymd-His')),

                        DatePicker::make('return_date')
                            ->label('تاريخ المرتجع')
                            ->required()
                            ->default(now()),

                        Select::make('purchase_invoice_id')
                            ->label('فاتورة الشراء الأصلية')
                            ->options(
                                fn () => PurchaseInvoice::query()
                                    ->where('status', PurchaseInvoice::STATUS_POSTED)
                                    ->latest('invoice_date')
                                    ->pluck('invoice_number', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $invoice = PurchaseInvoice::find($state);

                                $set('supplier_id', $invoice?->supplier_id);
                                $set('warehouse_id', $invoice?->warehouse_id);
                                $set('items', []);
                            }),

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

                        Select::make('refund_type')
                            ->label('طريقة رد القيمة')
                            ->options([
                                PurchaseReturn::REFUND_CASH => 'استرداد نقدي',
                                PurchaseReturn::REFUND_SUPPLIER_BALANCE => 'خصم من رصيد المورد',
                            ])
                            ->default(PurchaseReturn::REFUND_SUPPLIER_BALANCE)
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
                                    ->options(function (Get $get): array {
                                        $invoiceId = $get('../../purchase_invoice_id');

                                        if (! $invoiceId) {
                                            return [];
                                        }

                                        return PurchaseInvoiceItem::query()
                                            ->with('item')
                                            ->where('purchase_invoice_id', $invoiceId)
                                            ->get()
                                            ->mapWithKeys(function (PurchaseInvoiceItem $line) use ($invoiceId) {
                                                $originalQty = (float) PurchaseInvoiceItem::query()
                                                    ->where('purchase_invoice_id', $invoiceId)
                                                    ->where('item_id', $line->item_id)
                                                    ->sum('quantity');

                                                $returnedQty = (float) PurchaseReturnItem::query()
                                                    ->where('item_id', $line->item_id)
                                                    ->whereHas('purchaseReturn', function ($query) use ($invoiceId) {
                                                        $query
                                                            ->where('purchase_invoice_id', $invoiceId)
                                                            ->where('status', PurchaseReturn::STATUS_POSTED);
                                                    })
                                                    ->sum('quantity');

                                                $availableQty = max(0, $originalQty - $returnedQty);

                                                return [
                                                    $line->item_id => ($line->item?->name ?? 'صنف غير معروف')
                                                        . ' — المتاح للمرتجع: '
                                                        . number_format($availableQty, 3),
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, Get $get, Set $set): void {
                                        $invoiceId = $get('../../purchase_invoice_id');

                                        if (! $invoiceId || ! $state) {
                                            return;
                                        }

                                        $line = PurchaseInvoiceItem::query()
                                            ->where('purchase_invoice_id', $invoiceId)
                                            ->where('item_id', $state)
                                            ->first();

                                        $set('unit_id', $line?->unit_id);
                                        $set('unit_price', $line?->unit_price ?? 0);
                                        $set('discount_percent', $line?->discount_percent ?? 0);
                                        $set('purchase_invoice_item_id', $line?->id);
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
                                    ->minValue(0.001),

                                TextInput::make('unit_price')
                                    ->label('سعر الشراء')
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