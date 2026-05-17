<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BankAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('branch.name')
                    ->label('Branch')
                    ->placeholder('-'),
                TextEntry::make('code'),
                TextEntry::make('bank_name'),
                TextEntry::make('account_name'),
                TextEntry::make('account_number')
                    ->placeholder('-'),
                TextEntry::make('iban')
                    ->placeholder('-'),
                TextEntry::make('opening_balance')
                    ->numeric(),
                TextEntry::make('current_balance')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
