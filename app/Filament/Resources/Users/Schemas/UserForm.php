<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('اسم المستخدم')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                    ->helperText('اتركها فارغة عند التعديل إذا كنت لا تريد تغيير كلمة المرور.'),

                Select::make('roles')
                    ->label('الدور / الصلاحية')
                    ->relationship('roles', 'name')
                    ->options(fn (): array => Role::query()
                        ->whereIn('name', ['admin', 'employee'])
                        ->orderBy('name')
                        ->pluck('name', 'name')
                        ->mapWithKeys(fn (string $role): array => [
                            $role => match ($role) {
                                'admin' => 'مدير النظام',
                                'employee' => 'موظف بيع',
                                default => $role,
                            },
                        ])
                        ->toArray())
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->helperText('اختر admin للمدير أو employee لموظف نقطة البيع.'),
            ]);
    }
}