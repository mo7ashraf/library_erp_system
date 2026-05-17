<?php

namespace App\Filament\Resources\StockMovements\Tables;

use App\Models\StockMovement;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('movement_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('reference_number')
                    ->label('رقم المرجع')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('movement_type')
                    ->label('نوع الحركة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        StockMovement::TYPE_OPENING_BALANCE => 'رصيد افتتاحي',
                        StockMovement::TYPE_PURCHASE => 'مشتريات',
                        StockMovement::TYPE_PURCHASE_RETURN => 'مرتجع مشتريات',
                        StockMovement::TYPE_SALE => 'مبيعات',
                        StockMovement::TYPE_SALE_RETURN => 'مرتجع مبيعات',
                        StockMovement::TYPE_TRANSFER_IN => 'تحويل وارد',
                        StockMovement::TYPE_TRANSFER_OUT => 'تحويل صادر',
                        StockMovement::TYPE_STOCK_COUNT_INCREASE => 'زيادة جرد',
                        StockMovement::TYPE_STOCK_COUNT_DECREASE => 'عجز جرد',
                        StockMovement::TYPE_DAMAGED => 'تالف / هالك',
                        StockMovement::TYPE_MANUAL_ADJUSTMENT => 'تسوية يدوية',
                        default => $state ?? '-',
                    })
                    ->badge()
                    ->sortable(),

                TextColumn::make('direction')
                    ->label('الاتجاه')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        StockMovement::DIRECTION_IN => 'وارد',
                        StockMovement::DIRECTION_OUT => 'صادر',
                        default => '-',
                    })
                    ->badge()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('الكمية')
                    ->numeric(decimalPlaces: 3)
                    ->sortable(),

                TextColumn::make('balance_after')
                    ->label('الرصيد بعد الحركة')
                    ->numeric(decimalPlaces: 3)
                    ->sortable(),

                TextColumn::make('unit_cost')
                    ->label('تكلفة الوحدة')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2) . ' ج.م')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('total_cost')
                    ->label('إجمالي التكلفة')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2) . ' ج.م')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('item.code')
                    ->label('كود الصنف')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('item.name')
                    ->label('اسم الصنف')
                    ->searchable()
                    ->limit(35)
                    ->toggleable(),

                TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('وقت التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                SelectFilter::make('movement_type')
                    ->label('نوع الحركة')
                    ->options([
                        StockMovement::TYPE_OPENING_BALANCE => 'رصيد افتتاحي',
                        StockMovement::TYPE_PURCHASE => 'مشتريات',
                        StockMovement::TYPE_PURCHASE_RETURN => 'مرتجع مشتريات',
                        StockMovement::TYPE_SALE => 'مبيعات',
                        StockMovement::TYPE_SALE_RETURN => 'مرتجع مبيعات',
                        StockMovement::TYPE_TRANSFER_IN => 'تحويل وارد',
                        StockMovement::TYPE_TRANSFER_OUT => 'تحويل صادر',
                        StockMovement::TYPE_STOCK_COUNT_INCREASE => 'زيادة جرد',
                        StockMovement::TYPE_STOCK_COUNT_DECREASE => 'عجز جرد',
                        StockMovement::TYPE_DAMAGED => 'تالف / هالك',
                        StockMovement::TYPE_MANUAL_ADJUSTMENT => 'تسوية يدوية',
                    ]),

                SelectFilter::make('direction')
                    ->label('الاتجاه')
                    ->options([
                        StockMovement::DIRECTION_IN => 'وارد',
                        StockMovement::DIRECTION_OUT => 'صادر',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('عرض'),
            ]);
    }
}