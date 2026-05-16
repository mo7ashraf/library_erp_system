<?php

namespace App\Filament\Resources\OpeningStockDocuments\Tables;

use App\Models\OpeningStockDocument;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OpeningStockDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('document_date', 'desc')
            ->columns([
                TextColumn::make('reference_number')
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

                TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        OpeningStockDocument::STATUS_DRAFT => 'مسودة',
                        OpeningStockDocument::STATUS_POSTED => 'مرحل',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        OpeningStockDocument::STATUS_DRAFT => 'مسودة',
                        OpeningStockDocument::STATUS_POSTED => 'مرحل',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}