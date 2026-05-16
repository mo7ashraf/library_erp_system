<?php

namespace App\Filament\Resources\ItemGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('code')
                    ->label('كود المجموعة')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                TextInput::make('name')
                    ->label('اسم المجموعة')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

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