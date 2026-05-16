<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('code')
                    ->label('كود المخزن')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                TextInput::make('name')
                    ->label('اسم المخزن')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('account_code')
                    ->label('الحساب في الشجرة')
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),

                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}