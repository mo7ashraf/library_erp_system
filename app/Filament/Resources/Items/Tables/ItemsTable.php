<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_group_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('item_subgroup_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('baseUnit.name')
                    ->searchable(),
                TextColumn::make('middleUnit.name')
                    ->searchable(),
                TextColumn::make('largeUnit.name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('origin_code')
                    ->searchable(),
                TextColumn::make('barcode')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('publisher')
                    ->searchable(),
                TextColumn::make('purchase_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('first_discount_percent')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('second_discount_percent')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('net_purchase_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('profit_margin_percent')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('student_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('teacher_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('representative_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('retail_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('wholesale_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('teacher_discount_percent')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('representative_discount_percent')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('return_percent')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('min_stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reorder_level')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('units_per_middle')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('units_per_large')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image_path'),
                IconColumn::make('continue_balance')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
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
