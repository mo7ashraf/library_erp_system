<?php

namespace App\Filament\Resources\StockTransfers\Tables;

use App\Models\StockTransfer;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('transfer_date', 'desc')
            ->columns([
                TextColumn::make('transfer_number')
                    ->label('رقم الإذن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('transfer_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('fromWarehouse.name')
                    ->label('من المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('toWarehouse.name')
                    ->label('إلى المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        StockTransfer::STATUS_DRAFT => 'مسودة',
                        StockTransfer::STATUS_POSTED => 'مرحلة',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('total_quantity')
                    ->label('إجمالي الكمية')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_cost')
                    ->label('إجمالي التكلفة')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('from_warehouse_id')
                    ->label('من المخزن')
                    ->relationship('fromWarehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('to_warehouse_id')
                    ->label('إلى المخزن')
                    ->relationship('toWarehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        StockTransfer::STATUS_DRAFT => 'مسودة',
                        StockTransfer::STATUS_POSTED => 'مرحلة',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),

                Action::make('print_receipt')
                    ->label('طباعة')
                    ->url(fn (StockTransfer $record): string => route('admin.prints.stock-transfers.receipt', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}