<?php

namespace App\Filament\Resources\SalesInvoices\Schemas;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesInvoice;
use App\Models\WarehouseItemBalance;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SalesInvoiceForm
{
    private static function priceTypeForCustomer(?int $customerId): string
    {
        if (! $customerId) {
            return SalesInvoice::PRICE_STUDENT;
        }

        $customer = Customer::find($customerId);

        return match ($customer?->type) {
            'teacher' => SalesInvoice::PRICE_TEACHER,
            'representative' => SalesInvoice::PRICE_REPRESENTATIVE,
            'wholesale' => SalesInvoice::PRICE_WHOLESALE,
            default => SalesInvoice::PRICE_STUDENT,
        };
    }

    private static function availableQuantity(?int $warehouseId, ?int $itemId): float
    {
        if (! $warehouseId || ! $itemId) {
            return 0;
        }

        return (float) WarehouseItemBalance::query()
            ->where('warehouse_id', $warehouseId)
            ->where('item_id', $itemId)
            ->value('quantity');
    }

    private static function availableItemsForWarehouse(?int $warehouseId): array
    {
        if (! $warehouseId) {
            return [];
        }

        return WarehouseItemBalance::query()
            ->with('item')
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->get()
            ->filter(fn (WarehouseItemBalance $balance): bool => filled($balance->item))
            ->mapWithKeys(function (WarehouseItemBalance $balance): array {
                return [
                    $balance->item_id => ($balance->item?->name ?? 'صنف غير معروف')
                        . ' — المتاح: '
                        . number_format((float) $balance->quantity, 3),
                ];
            })
            ->toArray();
    }

    private static function itemPrice(?int $itemId, ?int $customerId, ?string $priceType): float
    {
        if (! $itemId) {
            return 0;
        }

        $item = Item::find($itemId);

        if (! $item) {
            return 0;
        }

        $customer = $customerId ? Customer::find($customerId) : null;

        if ($customer?->sales_at_purchase_price) {
            return (float) ($item->net_purchase_price ?: $item->purchase_price ?: 0);
        }

        return match ($priceType) {
            SalesInvoice::PRICE_TEACHER => (float) $item->teacher_price,
            SalesInvoice::PRICE_REPRESENTATIVE => (float) $item->representative_price,
            SalesInvoice::PRICE_RETAIL => (float) $item->retail_price,
            SalesInvoice::PRICE_WHOLESALE => (float) $item->wholesale_price,
            default => (float) $item->student_price,
        };
    }

    private static function itemDefaultUnitId(?int $itemId): ?int
    {
        if (! $itemId) {
            return null;
        }

        return Item::query()
            ->whereKey($itemId)
            ->value('base_unit_id');
    }

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
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $set('price_type', self::priceTypeForCustomer($state));
                            }),

                        Select::make('warehouse_id')
                            ->label('المخزن')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('items', []);
                            }),

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
                            ->live()
                            ->required(),

                        TextInput::make('discount_amount')
                            ->label('خصم على الفاتورة')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
                            ->prefix('ج.م'),

                        TextInput::make('service_amount')
                            ->label('خدمات')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
                            ->prefix('ج.م'),

                        TextInput::make('commission_percent')
                            ->label('عمولة %')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
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
                                    ->options(fn (Get $get): array => self::availableItemsForWarehouse(
                                        $get('../../warehouse_id'),
                                    ))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, Get $get, Set $set): void {
                                        $customerId = $get('../../customer_id');
                                        $priceType = $get('../../price_type');

                                        $set('unit_id', self::itemDefaultUnitId($state));
                                        $set('unit_price', self::itemPrice($state, $customerId, $priceType));
                                        $set('quantity', null);
                                    })
                                    ->helperText(fn (Get $get): string => 'المتاح في المخزن: ' . number_format(
                                        self::availableQuantity(
                                            $get('../../warehouse_id'),
                                            $get('item_id'),
                                        ),
                                        3
                                    ))
                                    ->columnSpan(2),

                                Select::make('unit_id')
                                    ->label('الوحدة')
                                    ->relationship('unit', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.001)
                                    ->maxValue(fn (Get $get): float => self::availableQuantity(
                                        $get('../../warehouse_id'),
                                        $get('item_id'),
                                    ))
                                    ->helperText(fn (Get $get): string => 'الحد الأقصى المتاح: ' . number_format(
                                        self::availableQuantity(
                                            $get('../../warehouse_id'),
                                            $get('item_id'),
                                        ),
                                        3
                                    ))
                                    ->validationAttribute('كمية البيع'),

                                TextInput::make('unit_price')
                                    ->label('سعر البيع')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.01)
                                    ->validationMessages([
                                        'min' => 'لا يمكن إنشاء فاتورة بيع بسعر صفر. اختر الصنف مرة أخرى أو راجع أسعار الصنف.',
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