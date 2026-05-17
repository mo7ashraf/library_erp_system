<?php

namespace App\Filament\Resources\StockCountDocuments\Tables;

use App\Models\StockCountDocument;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;

class StockCountDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('count_date', 'desc')
            ->columns([
                TextColumn::make('count_number')
                    ->label('رقم المحضر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('count_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        StockCountDocument::STATUS_DRAFT => 'مسودة',
                        StockCountDocument::STATUS_POSTED => 'مرحلة',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('total_increase_quantity')
                    ->label('إجمالي الزيادة')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_decrease_quantity')
                    ->label('إجمالي العجز')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_difference_cost')
                    ->label('قيمة الفرق')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label('المخزن')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        StockCountDocument::STATUS_DRAFT => 'مسودة',
                        StockCountDocument::STATUS_POSTED => 'مرحلة',
                    ]),
            ])
           ->recordActions([
                ViewAction::make()->label('عرض'),

                Action::make('print_receipt')
                    ->label('طباعة')
                    ->url(fn (StockCountDocument $record): string => route('admin.prints.stock-count-documents.receipt', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}