<?php

namespace App\Filament\Resources\WarehouseItemBalances\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WarehouseItemBalanceForm
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
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('average_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('total_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
            ]);
    }
}
