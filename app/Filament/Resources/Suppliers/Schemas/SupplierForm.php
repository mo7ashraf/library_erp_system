<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->relationship('branch', 'name'),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('mobile'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('governorate'),
                TextInput::make('city'),
                TextInput::make('address'),
                TextInput::make('opening_balance')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('balance_type')
                    ->required()
                    ->default('credit'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
