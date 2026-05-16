<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('code')
                    ->label('كود العميل')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                TextInput::make('name')
                    ->label('اسم العميل')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                Select::make('type')
                    ->label('نوع العميل')
                    ->options([
                        'student' => 'طالب',
                        'teacher' => 'مدرس',
                        'representative' => 'مندوب',
                        'wholesale' => 'جملة',
                        'other' => 'أخرى',
                    ])
                    ->default('student')
                    ->required(),

                TextInput::make('phone')
                    ->label('الهاتف')
                    ->tel()
                    ->maxLength(50),

                TextInput::make('mobile')
                    ->label('الموبايل')
                    ->tel()
                    ->maxLength(50),

                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->maxLength(255),

                TextInput::make('governorate')
                    ->label('المحافظة')
                    ->maxLength(100),

                TextInput::make('city')
                    ->label('المدينة / المركز')
                    ->maxLength(100),

                TextInput::make('address')
                    ->label('العنوان')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('opening_balance')
                    ->label('الرصيد الافتتاحي')
                    ->numeric()
                    ->default(0),

                Select::make('balance_type')
                    ->label('نوع الرصيد')
                    ->options([
                        'debit' => 'مدين / عليه',
                        'credit' => 'دائن / له',
                    ])
                    ->default('debit')
                    ->required(),

                TextInput::make('discount_percent')
                    ->label('نسبة الخصم %')
                    ->numeric()
                    ->default(0),

                Toggle::make('sales_at_purchase_price')
                    ->label('مبيعاته بسعر الشراء')
                    ->default(false),

                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),

                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}