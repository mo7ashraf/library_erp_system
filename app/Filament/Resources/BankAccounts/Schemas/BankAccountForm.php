<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
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
                    ->label('كود الحساب')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                TextInput::make('bank_name')
                    ->label('اسم البنك')
                    ->required()
                    ->maxLength(255),

                TextInput::make('account_name')
                    ->label('اسم الحساب')
                    ->required()
                    ->maxLength(255),

                TextInput::make('account_number')
                    ->label('رقم الحساب')
                    ->maxLength(100),

                TextInput::make('iban')
                    ->label('IBAN')
                    ->maxLength(100),

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
                    ->label('نشط')
                    ->default(true),

                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }
}
