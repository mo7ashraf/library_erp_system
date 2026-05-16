<?php

namespace App\Filament\Resources\ItemGroups\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ItemGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('code')
                    ->label('كود المجموعة'),

                TextEntry::make('name')
                    ->label('اسم المجموعة'),

                IconEntry::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                TextEntry::make('notes')
                    ->label('ملاحظات')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}