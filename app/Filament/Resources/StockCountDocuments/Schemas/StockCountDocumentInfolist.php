<?php

namespace App\Filament\Resources\StockCountDocuments\Schemas;

use App\Models\StockCountDocument;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockCountDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('count_number')
                    ->label('رقم محضر الجرد'),

                TextEntry::make('count_date')
                    ->label('تاريخ الجرد')
                    ->date('Y-m-d'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        StockCountDocument::STATUS_DRAFT => 'مسودة',
                        StockCountDocument::STATUS_POSTED => 'مرحلة',
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

                        TextEntry::make('system_quantity')
                            ->label('رصيد النظام'),

                        TextEntry::make('actual_quantity')
                            ->label('الرصيد الفعلي'),

                        TextEntry::make('difference_quantity')
                            ->label('الفرق'),

                        TextEntry::make('unit_cost')
                            ->label('متوسط التكلفة')
                            ->money('EGP'),

                        TextEntry::make('difference_cost')
                            ->label('قيمة الفرق')
                            ->money('EGP'),

                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull(),

                TextEntry::make('total_increase_quantity')
                    ->label('إجمالي الزيادة'),

                TextEntry::make('total_decrease_quantity')
                    ->label('إجمالي العجز'),

                TextEntry::make('total_difference_cost')
                    ->label('إجمالي قيمة الفرق')
                    ->money('EGP'),

                TextEntry::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}