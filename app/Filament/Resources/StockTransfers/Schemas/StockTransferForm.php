<?php

namespace App\Filament\Resources\StockTransfers\Schemas;

use App\Models\Item;
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

class StockTransferForm
{
    private static function itemDefaultUnitId(?int $itemId): ?int
    {
        if (! $itemId) {
            return null;
        }

        return Item::query()
            ->whereKey($itemId)
            ->value('base_unit_id');
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

    private static function itemAverageCost(?int $warehouseId, ?int $itemId): float
    {
        if (! $warehouseId || ! $itemId) {
            return 0;
        }

        return (float) WarehouseItemBalance::query()
            ->where('warehouse_id', $warehouseId)
            ->where('item_id', $itemId)
            ->value('average_cost');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات التحويل')
                    ->columns(3)
                    ->schema([
                        TextInput::make('transfer_number')
                            ->label('رقم إذن التحويل')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'TRN-' . now()->format('Ymd-His')),

                        DatePicker::make('transfer_date')
                            ->label('تاريخ التحويل')
                            ->required()
                            ->default(now()),

                        Select::make('from_warehouse_id')
                            ->label('من المخزن')
                            ->relationship('fromWarehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('items', []);
                            })
                            ->required(),

                        Select::make('to_warehouse_id')
                            ->label('إلى المخزن')
                            ->relationship('toWarehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->different('from_warehouse_id'),

                        Textarea::make('notes')
                            ->label('بيان / ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('الأصناف')
                    ->schema([
                        Repeater::make('items')
                            ->label('الأصناف')
                            ->relationship('items')
                            ->columns(5)
                            ->schema([
                                Select::make('item_id')
                                    ->label('الصنف')
                                    ->options(fn (Get $get): array => self::availableItemsForWarehouse(
                                        $get('../../from_warehouse_id'),
                                    ))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, Get $get, Set $set): void {
                                        $fromWarehouseId = $get('../../from_warehouse_id');

                                        $set('unit_id', self::itemDefaultUnitId($state));
                                        $set('unit_cost', self::itemAverageCost($fromWarehouseId, $state));
                                        $set('quantity', null);
                                    })
                                    ->helperText(fn (Get $get): string => 'المتاح في المخزن المصدر: ' . number_format(
                                        self::availableQuantity(
                                            $get('../../from_warehouse_id'),
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
                                        $get('../../from_warehouse_id'),
                                        $get('item_id'),
                                    ))
                                    ->helperText(fn (Get $get): string => 'الحد الأقصى المتاح: ' . number_format(
                                        self::availableQuantity(
                                            $get('../../from_warehouse_id'),
                                            $get('item_id'),
                                        ),
                                        3
                                    ))
                                    ->validationAttribute('كمية التحويل'),

                                TextInput::make('unit_cost')
                                    ->label('تكلفة الوحدة')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->dehydrateStateUsing(fn ($state): float => (float) ($state ?: 0))
                                    ->prefix('ج.م'),

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