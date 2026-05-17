<?php

namespace App\Filament\Resources\StockTransfers\Schemas;

use App\Models\StockTransfer;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockTransferInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('transfer_number')
                    ->label('رقم إذن التحويل'),

                TextEntry::make('transfer_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),

                TextEntry::make('fromWarehouse.name')
                    ->label('من المخزن'),

                TextEntry::make('toWarehouse.name')
                    ->label('إلى المخزن'),

                TextEntry::make('fromBranch.name')
                    ->label('من الفرع')
                    ->placeholder('-'),

                TextEntry::make('toBranch.name')
                    ->label('إلى الفرع')
                    ->placeholder('-'),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        StockTransfer::STATUS_DRAFT => 'مسودة',
                        StockTransfer::STATUS_POSTED => 'مرحلة',
                        default => '-',
                    }),

                TextEntry::make('user.name')
                    ->label('المستخدم')
                    ->placeholder('-'),

                TextEntry::make('notes')
                    ->label('ملاحظات')
                    ->placeholder('-')
                    ->columnSpanFull(),

                RepeatableEntry::make('items')
                    ->label('الأصناف')
                    ->columns(5)
                    ->schema([
                        TextEntry::make('item.code')
                            ->label('كود الصنف'),

                        TextEntry::make('item.name')
                            ->label('اسم الصنف'),

                        TextEntry::make('unit.name')
                            ->label('الوحدة')
                            ->placeholder('-'),

                        TextEntry::make('quantity')
                            ->label('الكمية'),

                        TextEntry::make('unit_cost')
                            ->label('تكلفة الوحدة')
                            ->money('EGP'),

                        TextEntry::make('total_cost')
                            ->label('الإجمالي')
                            ->money('EGP'),
                    ])
                    ->columnSpanFull(),

                TextEntry::make('total_quantity')
                    ->label('إجمالي الكمية'),

                TextEntry::make('total_cost')
                    ->label('إجمالي التكلفة')
                    ->money('EGP'),

                TextEntry::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}