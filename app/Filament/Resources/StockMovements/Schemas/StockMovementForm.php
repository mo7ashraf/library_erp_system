<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->required(),
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->required(),
                Select::make('branch_id')
                    ->relationship('branch', 'name'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('movement_type')
                    ->required(),
                TextInput::make('direction')
                    ->required(),
                TextInput::make('reference_type'),
                TextInput::make('reference_id')
                    ->numeric(),
                TextInput::make('reference_number'),
                DatePicker::make('movement_date')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('unit_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('total_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('balance_after')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
