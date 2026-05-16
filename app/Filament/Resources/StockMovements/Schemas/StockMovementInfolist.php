<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use App\Models\StockMovement;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('movement_date')
                    ->label('تاريخ الحركة')
                    ->date('Y-m-d'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('item.code')
                    ->label('كود الصنف'),

                TextEntry::make('item.name')
                    ->label('اسم الصنف')
                    ->columnSpanFull(),

                TextEntry::make('movement_type')
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
                    }),

                TextEntry::make('direction')
                    ->label('الاتجاه')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        StockMovement::DIRECTION_IN => 'وارد',
                        StockMovement::DIRECTION_OUT => 'صادر',
                        default => '-',
                    }),

                TextEntry::make('quantity')
                    ->label('الكمية'),

                TextEntry::make('unit_cost')
                    ->label('تكلفة الوحدة')
                    ->money('EGP'),

                TextEntry::make('total_cost')
                    ->label('إجمالي التكلفة')
                    ->money('EGP'),

                TextEntry::make('balance_after')
                    ->label('الرصيد بعد الحركة'),

                TextEntry::make('reference_number')
                    ->label('رقم المرجع')
                    ->placeholder('-'),

                TextEntry::make('user.name')
                    ->label('المستخدم')
                    ->placeholder('-'),

                TextEntry::make('notes')
                    ->label('ملاحظات')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i'),
            ]);
    }
}