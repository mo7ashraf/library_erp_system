<?php

namespace App\Filament\Resources\StockTransfers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockTransferForm
{
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

                                TextInput::make('unit_cost')
                                    ->label('تكلفة الوحدة')
                                    ->numeric()
                                    ->default(0)
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