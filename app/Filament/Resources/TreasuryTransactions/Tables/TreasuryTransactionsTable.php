<?php

namespace App\Filament\Resources\TreasuryTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TreasuryTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('cashbox.name')
                    ->searchable(),
                TextColumn::make('bankAccount.id')
                    ->searchable(),
                TextColumn::make('transaction_number')
                    ->searchable(),
                TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_channel')
                    ->searchable(),
                TextColumn::make('direction')
                    ->searchable(),
                TextColumn::make('transaction_type')
                    ->searchable(),
                TextColumn::make('party_type')
                    ->searchable(),
                TextColumn::make('party_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('party_name')
                    ->searchable(),
                TextColumn::make('reference_type')
                    ->searchable(),
                TextColumn::make('reference_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('balance_after')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
