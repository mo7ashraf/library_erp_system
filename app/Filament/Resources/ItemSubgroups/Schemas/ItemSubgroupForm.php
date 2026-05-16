<?php

namespace App\Filament\Resources\ItemSubgroups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemSubgroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('item_group_id')
                    ->label('يتبع المجموعة الرئيسية')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('code')
                    ->label('كود المجموعة الفرعية')
                    ->required()
                    ->maxLength(50),

                TextInput::make('name')
                    ->label('اسم المجموعة الفرعية')
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