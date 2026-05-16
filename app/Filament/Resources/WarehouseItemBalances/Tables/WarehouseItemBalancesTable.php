<?php

namespace App\Filament\Resources\WarehouseItemBalances\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WarehouseItemBalancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('warehouse.branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.code')
                    ->label('كود الصنف')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.barcode')
                    ->label('الباركود')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('item.name')
                    ->label('اسم الصنف')
                    ->searchable()
                    ->limit(45),

                TextColumn::make('item.group.name')
                    ->label('المجموعة')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('quantity')
                    ->label('الرصيد الحالي')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('average_cost')
                    ->label('متوسط التكلفة')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('total_cost')
                    ->label('إجمالي القيمة')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label('المخزن')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('item_id')
                    ->label('الصنف')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}