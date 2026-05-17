<?php

namespace App\Filament\Resources\DamagedStockDocuments\Schemas;

use App\Models\DamagedStockDocument;
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
use Illuminate\Validation\Rules\Numeric;

class DamagedStockDocumentForm
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
                Section::make('بيانات الإذن')
                    ->columns(3)
                    ->schema([
                        TextInput::make('document_number')
                            ->label('رقم الإذن')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'DMG-' . now()->format('Ymd-His')),

                        DatePicker::make('document_date')
                            ->label('تاريخ الإذن')
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

                        Select::make('reason_type')
                            ->label('سبب الخروج')
                            ->options([
                                DamagedStockDocument::REASON_DAMAGED => 'تالف',
                                DamagedStockDocument::REASON_LOST => 'مفقود',
                                DamagedStockDocument::REASON_EXPIRED => 'منتهي / غير صالح',
                                DamagedStockDocument::REASON_OTHER => 'أخرى',
                            ])
                            ->default(DamagedStockDocument::REASON_DAMAGED)
                            ->required(),

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
                            ->columns(6)
                            ->schema([
                                 Hidden::make('unit_cost'),

                                TextInput::make('available_quantity')
                                    ->label('الرصيد المتاح')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(0),

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

                                        $set('available_quantity', $balance['quantity']);
                                        $set('unit_cost', $balance['average_cost']);
                                        $set('quantity', null);
                                    })
                                    ->helperText(function (Get $get): string {
                                        $warehouseId = $get('../../warehouse_id');
                                        $itemId = $get('item_id');

                                        $balance = self::getCurrentBalance($warehouseId, $itemId);

                                        return 'الرصيد المتاح: '
                                            . number_format($balance['quantity'], 3)
                                            . ' | متوسط التكلفة: '
                                            . number_format($balance['average_cost'], 2);
                                    }),

                                Select::make('unit_id')
                                    ->label('الوحدة')
                                    ->relationship('unit', 'name')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.001)
                                    ->helperText(fn (Get $get): string => 'الحد الأقصى المسموح: ' . number_format(
                                        self::getCurrentBalance(
                                            $get('../../warehouse_id'),
                                            $get('item_id'),
                                        )['quantity'],
                                        3
                                    ))
                                    ->rule(function (Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                                            $warehouseId = $get('../../warehouse_id');
                                            $itemId = $get('item_id');

                                            $availableQty = self::getCurrentBalance($warehouseId, $itemId)['quantity'];

                                            if ((float) $value > (float) $availableQty) {
                                                $fail("لا يمكن إخراج كمية {$value}. الرصيد المتاح هو {$availableQty} فقط.");
                                            }
                                        };
                                    }),

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