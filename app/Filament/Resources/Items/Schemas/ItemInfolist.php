<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('item_group_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('item_subgroup_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('baseUnit.name')
                    ->label('Base unit')
                    ->placeholder('-'),
                TextEntry::make('middleUnit.name')
                    ->label('Middle unit')
                    ->placeholder('-'),
                TextEntry::make('largeUnit.name')
                    ->label('Large unit')
                    ->placeholder('-'),
                TextEntry::make('code'),
                TextEntry::make('origin_code')
                    ->placeholder('-'),
                TextEntry::make('barcode')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('source')
                    ->placeholder('-'),
                TextEntry::make('publisher')
                    ->placeholder('-'),
                TextEntry::make('purchase_price')
                    ->money(),
                TextEntry::make('first_discount_percent')
                    ->numeric(),
                TextEntry::make('second_discount_percent')
                    ->numeric(),
                TextEntry::make('net_purchase_price')
                    ->money(),
                TextEntry::make('total_cost')
                    ->money(),
                TextEntry::make('profit_margin_percent')
                    ->numeric(),
                TextEntry::make('student_price')
                    ->money(),
                TextEntry::make('teacher_price')
                    ->money(),
                TextEntry::make('representative_price')
                    ->money(),
                TextEntry::make('retail_price')
                    ->money(),
                TextEntry::make('wholesale_price')
                    ->money(),
                TextEntry::make('teacher_discount_percent')
                    ->numeric(),
                TextEntry::make('representative_discount_percent')
                    ->numeric(),
                TextEntry::make('return_percent')
                    ->numeric(),
                TextEntry::make('max_stock')
                    ->numeric(),
                TextEntry::make('min_stock')
                    ->numeric(),
                TextEntry::make('reorder_level')
                    ->numeric(),
                TextEntry::make('units_per_middle')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('units_per_large')
                    ->numeric()
                    ->placeholder('-'),
                ImageEntry::make('image_path')
                    ->placeholder('-'),
                TextEntry::make('details')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('continue_balance')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
