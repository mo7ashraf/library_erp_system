<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('code')
                    ->label('كود الوحدة')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                TextInput::make('name')
                    ->label('اسم الوحدة')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('symbol')
                    ->label('الاختصار / الرمز')
                    ->maxLength(50),

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