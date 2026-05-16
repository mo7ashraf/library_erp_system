<?php

namespace App\Filament\Resources\OpeningStockDocuments\Schemas;

use App\Models\OpeningStockDocument;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OpeningStockDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('reference_number')
                    ->label('رقم الإذن'),

                TextEntry::make('document_date')
                    ->label('التاريخ')
                    ->date('Y-m-d'),

                TextEntry::make('warehouse.name')
                    ->label('المخزن'),

                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        OpeningStockDocument::STATUS_DRAFT => 'مسودة',
                        OpeningStockDocument::STATUS_POSTED => 'مرحل',
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
                    ->columns(4)
                    ->schema([
                        TextEntry::make('item.code')
                            ->label('كود الصنف'),

                        TextEntry::make('item.name')
                            ->label('اسم الصنف'),

                        TextEntry::make('quantity')
                            ->label('الكمية'),

                        TextEntry::make('unit_cost')
                            ->label('تكلفة الوحدة')
                            ->money('EGP'),

                        TextEntry::make('total_cost')
                            ->label('الإجمالي')
                            ->money('EGP'),

                        TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->placeholder('-'),
                    ])
                    ->columnSpanFull(),

                TextEntry::make('posted_at')
                    ->label('تاريخ الترحيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}