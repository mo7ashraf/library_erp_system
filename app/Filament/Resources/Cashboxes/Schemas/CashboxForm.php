<?php

namespace App\Filament\Resources\Cashboxes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CashboxForm
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
                ->preload(),

            TextInput::make('code')
                ->label('كود الخزينة')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),

            TextInput::make('name')
                ->label('اسم الخزينة')
                ->required()
                ->maxLength(255),

            TextInput::make('opening_balance')
                ->label('الرصيد الافتتاحي')
                ->numeric()
                ->default(0)
                ->prefix('ج.م'),

            TextInput::make('current_balance')
                ->label('الرصيد الحالي')
                ->numeric()
                ->default(0)
                ->prefix('ج.م'),

            Toggle::make('is_active')
                ->label('نشطة')
                ->default(true),

            Textarea::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull(),
        ]);
    }
}
