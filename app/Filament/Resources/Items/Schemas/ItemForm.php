<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('item_group_id')
                    ->numeric(),
                TextInput::make('item_subgroup_id')
                    ->numeric(),
                Select::make('base_unit_id')
                    ->relationship('baseUnit', 'name'),
                Select::make('middle_unit_id')
                    ->relationship('middleUnit', 'name'),
                Select::make('large_unit_id')
                    ->relationship('largeUnit', 'name'),
                TextInput::make('code')
                    ->required(),
                TextInput::make('origin_code'),
                TextInput::make('barcode'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('source'),
                TextInput::make('publisher'),
                TextInput::make('purchase_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('first_discount_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('second_discount_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('net_purchase_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('total_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('profit_margin_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('student_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('teacher_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('representative_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('retail_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('wholesale_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('teacher_discount_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('representative_discount_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('return_percent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('max_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('min_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('reorder_level')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('units_per_middle')
                    ->numeric(),
                TextInput::make('units_per_large')
                    ->numeric(),
                FileUpload::make('image_path')
                    ->image(),
                Textarea::make('details')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('continue_balance')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
