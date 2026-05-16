<?php

namespace App\Filament\Resources\WarehouseItemBalances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WarehouseItemBalanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('warehouse.branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('item.code')
                    ->label('كود الصنف'),

                TextEntry::make('item.barcode')
                    ->label('الباركود')
                    ->placeholder('-'),

                TextEntry::make('item.name')
                    ->label('اسم الصنف')
                    ->columnSpanFull(),

                TextEntry::make('item.group.name')
                    ->label('المجموعة')
                    ->placeholder('-'),

                TextEntry::make('item.subgroup.name')
                    ->label('المجموعة الفرعية')
                    ->placeholder('-'),

                TextEntry::make('quantity')
                    ->label('الرصيد الحالي'),

                TextEntry::make('average_cost')
                    ->label('متوسط التكلفة')
                    ->money('EGP'),

                TextEntry::make('total_cost')
                    ->label('إجمالي القيمة')
                    ->money('EGP'),

                TextEntry::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i'),
            ]);
    }
}