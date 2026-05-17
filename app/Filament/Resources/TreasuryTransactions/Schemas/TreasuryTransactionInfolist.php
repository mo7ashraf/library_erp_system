<?php

namespace App\Filament\Resources\TreasuryTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TreasuryTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('branch.name')
                    ->label('Branch')
                    ->placeholder('-'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('cashbox.name')
                    ->label('Cashbox')
                    ->placeholder('-'),
                TextEntry::make('bankAccount.id')
                    ->label('Bank account')
                    ->placeholder('-'),
                TextEntry::make('transaction_number'),
                TextEntry::make('transaction_date')
                    ->date(),
                TextEntry::make('payment_channel'),
                TextEntry::make('direction'),
                TextEntry::make('transaction_type'),
                TextEntry::make('party_type')
                    ->placeholder('-'),
                TextEntry::make('party_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('party_name')
                    ->placeholder('-'),
                TextEntry::make('reference_type')
                    ->placeholder('-'),
                TextEntry::make('reference_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('reference_number')
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('balance_after')
                    ->numeric(),
                TextEntry::make('description')
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
