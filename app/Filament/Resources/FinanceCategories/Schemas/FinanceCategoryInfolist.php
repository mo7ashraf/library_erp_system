<?php

namespace App\Filament\Resources\FinanceCategories\Schemas;

use App\Models\FinanceCategory;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FinanceCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('code')
                    ->label('الكود'),

                TextEntry::make('name')
                    ->label('اسم البند'),

                TextEntry::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        FinanceCategory::TYPE_EXPENSE => 'مصروف',
                        FinanceCategory::TYPE_INCOME => 'إيراد',
                        default => '-',
                    }),

                IconEntry::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                TextEntry::make('notes')
                    ->label('ملاحظات')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i'),
            ]);
    }
}