<?php

namespace App\Filament\Resources\OpeningStockDocuments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OpeningStockDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات الإذن')
                    ->columns(3)
                    ->schema([
                        TextInput::make('reference_number')
                            ->label('رقم الإذن')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->default(fn (): string => 'OPEN-' . now()->format('Ymd-His')),

                        DatePicker::make('document_date')
                            ->label('تاريخ اليوم')
                            ->required()
                            ->default(now()),

                        Select::make('warehouse_id')
                            ->label('المخزن')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
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
                            ->columns(4)
                            ->schema([
                                Select::make('item_id')
                                    ->label('الصنف')
                                    ->relationship('item', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.001),

                                TextInput::make('unit_cost')
                                    ->label('تكلفة الوحدة')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->prefix('ج.م'),

                                TextInput::make('notes')
                                    ->label('ملاحظات')
                                    ->maxLength(255),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('إضافة صنف')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}