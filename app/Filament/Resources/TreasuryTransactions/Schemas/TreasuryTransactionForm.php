<?php

namespace App\Filament\Resources\TreasuryTransactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TreasuryTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->relationship('branch', 'name'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('cashbox_id')
                    ->relationship('cashbox', 'name'),
                Select::make('bank_account_id')
                    ->relationship('bankAccount', 'id'),
                TextInput::make('transaction_number')
                    ->required(),
                DatePicker::make('transaction_date')
                    ->required(),
                TextInput::make('payment_channel')
                    ->required(),
                TextInput::make('direction')
                    ->required(),
                TextInput::make('transaction_type')
                    ->required(),
                TextInput::make('party_type'),
                TextInput::make('party_id')
                    ->numeric(),
                TextInput::make('party_name'),
                TextInput::make('reference_type'),
                TextInput::make('reference_id')
                    ->numeric(),
                TextInput::make('reference_number'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('balance_after')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
