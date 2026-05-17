<?php

namespace App\Filament\Resources\DamagedStockDocuments\Tables;

use App\Models\DamagedStockDocument;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DamagedStockDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('document_date', 'desc')
            ->columns([
                TextColumn::make('document_number')
                    ->label('رقم الإذن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('document_date')
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

                TextColumn::make('reason_type')
                    ->label('السبب')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        DamagedStockDocument::REASON_DAMAGED => 'تالف',
                        DamagedStockDocument::REASON_LOST => 'مفقود',
                        DamagedStockDocument::REASON_EXPIRED => 'منتهي / غير صالح',
                        DamagedStockDocument::REASON_OTHER => 'أخرى',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        DamagedStockDocument::STATUS_DRAFT => 'مسودة',
                        DamagedStockDocument::STATUS_POSTED => 'مرحلة',
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
                SelectFilter::make('warehouse_id')
                    ->label('المخزن')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('reason_type')
                    ->label('السبب')
                    ->options([
                        DamagedStockDocument::REASON_DAMAGED => 'تالف',
                        DamagedStockDocument::REASON_LOST => 'مفقود',
                        DamagedStockDocument::REASON_EXPIRED => 'منتهي / غير صالح',
                        DamagedStockDocument::REASON_OTHER => 'أخرى',
                    ]),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        DamagedStockDocument::STATUS_DRAFT => 'مسودة',
                        DamagedStockDocument::STATUS_POSTED => 'مرحلة',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),

                Action::make('print_receipt')
                    ->label('طباعة')
                    ->url(fn (DamagedStockDocument $record): string => route('admin.prints.damaged-stock-documents.receipt', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}