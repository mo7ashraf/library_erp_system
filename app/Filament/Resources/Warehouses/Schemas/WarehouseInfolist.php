<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WarehouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('branch.name')
                    ->label('الفرع')
                    ->placeholder('-'),

                TextEntry::make('code')
                    ->label('كود المخزن'),

                TextEntry::make('name')
                    ->label('اسم المخزن'),

                TextEntry::make('account_code')
                    ->label('الحساب في الشجرة')
                    ->placeholder('-'),

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