<?php

namespace App\Filament\Resources\DamagedStockDocuments\Schemas;

use App\Models\DamagedStockDocument;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DamagedStockDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('document_number')
                    ->label('رقم الإذن'),

                TextEntry::make('document_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('reason_type')
                    ->label('سبب الخروج')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        DamagedStockDocument::REASON_DAMAGED => 'تالف',
                        DamagedStockDocument::REASON_LOST => 'مفقود',
                        DamagedStockDocument::REASON_EXPIRED => 'منتهي / غير صالح',
                        DamagedStockDocument::REASON_OTHER => 'أخرى',
                        default => '-',
                    }),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        DamagedStockDocument::STATUS_DRAFT => 'مسودة',
                        DamagedStockDocument::STATUS_POSTED => 'مرحلة',
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
                            ->label('متوسط التكلفة')
                            ->money('EGP'),

                        TextEntry::make('total_cost')
                            ->label('الإجمالي')
                            ->money('EGP'),

                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('-'),
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