<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SupplierInfolist
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
                    ->label('كود المورد'),

                TextEntry::make('name')
                    ->label('اسم المورد'),

                TextEntry::make('phone')
                    ->label('الهاتف')
                    ->placeholder('-'),

                TextEntry::make('mobile')
                    ->label('الموبايل')
                    ->placeholder('-'),

                TextEntry::make('email')
                    ->label('البريد الإلكتروني')
                    ->placeholder('-'),

                TextEntry::make('governorate')
                    ->label('المحافظة')
                    ->placeholder('-'),

                TextEntry::make('city')
                    ->label('المدينة / المركز')
                    ->placeholder('-'),

                TextEntry::make('address')
                    ->label('العنوان')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('opening_balance')
                    ->label('الرصيد الافتتاحي')
                    ->money('EGP'),

                TextEntry::make('balance_type')
                    ->label('نوع الرصيد')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'debit' => 'مدين / عليه',
                        'credit' => 'دائن / له',
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
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ]);
    }
}