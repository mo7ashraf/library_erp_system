<?php

namespace App\Filament\Resources\FinanceCategories\Tables;

use App\Models\FinanceCategory;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinanceCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('type')
            ->columns([
                TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('اسم البند')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        FinanceCategory::TYPE_EXPENSE => 'مصروف',
                        FinanceCategory::TYPE_INCOME => 'إيراد',
                        default => '-',
                    })
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        FinanceCategory::TYPE_EXPENSE => 'مصروف',
                        FinanceCategory::TYPE_INCOME => 'إيراد',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}