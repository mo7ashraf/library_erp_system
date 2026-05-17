<?php

namespace App\Filament\Resources\StockCountDocuments\Schemas;

use App\Models\WarehouseItemBalance;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StockCountDocumentForm
{
    private static function getCurrentBalance(?int $warehouseId, ?int $itemId): array
    {
        if (! $warehouseId || ! $itemId) {
            return [
                'quantity' => 0,
                'average_cost' => 0,
            ];
        }

        $balance = WarehouseItemBalance::query()
            ->where('warehouse_id', $warehouseId)
            ->where('item_id', $itemId)
            ->first();

        return [
            'quantity' => (float) ($balance?->quantity ?? 0),
            'average_cost' => (float) ($balance?->average_cost ?? 0),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات محضر الجرد')
                    ->columns(3)
                    ->schema([
                        TextInput::make('count_number')
                            ->label('رقم محضر الجرد')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'CNT-' . now()->format('Ymd-His')),

                        DatePicker::make('count_date')
                            ->label('تاريخ الجرد')
                            ->required()
                            ->default(now()),

                        Select::make('warehouse_id')
                            ->label('المخزن')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('items', [])),

                        Textarea::make('notes')
                            ->label('بيان / ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('الأصناف المجردة')
                    ->schema([
                        Repeater::make('items')
                            ->label('الأصناف')
                            ->relationship('items')
                            ->columns(6)
                            ->schema([
                                Hidden::make('system_quantity'),
                                Hidden::make('unit_cost'),

                                Select::make('item_id')
                                    ->label('الصنف')
                                    ->relationship('item', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function (?int $state, Get $get, Set $set): void {
                                        $warehouseId = $get('../../warehouse_id');
                                        $balance = self::getCurrentBalance($warehouseId, $state);

                                        $set('system_quantity', $balance['quantity']);
                                        $set('unit_cost', $balance['average_cost']);
                                        $set('actual_quantity', $balance['quantity']);
                                    })
                                    ->helperText(function (Get $get): string {
                                        $warehouseId = $get('../../warehouse_id');
                                        $itemId = $get('item_id');

                                        $balance = self::getCurrentBalance($warehouseId, $itemId);

                                        return 'الرصيد الحالي في النظام: '
                                            . number_format($balance['quantity'], 3)
                                            . ' | متوسط التكلفة: '
                                            . number_format($balance['average_cost'], 2);
                                    }),

                                Select::make('unit_id')
                                    ->label('الوحدة')
                                    ->relationship('unit', 'name')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('actual_quantity')
                                    ->label('الكمية الفعلية')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->helperText('أدخل الكمية الموجودة فعليًا أثناء الجرد.'),

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