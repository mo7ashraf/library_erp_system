<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('اسم المستخدم'),

                TextEntry::make('email')
                    ->label('البريد الإلكتروني'),

                TextEntry::make('roles.name')
                    ->label('الأدوار')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'مدير النظام',
                        'employee' => 'موظف بيع',
                        default => $state,
                    }),

                TextEntry::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i'),
            ]);
    }
}