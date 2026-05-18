<?php

namespace App\Filament\Resources\FinanceCategories\Schemas;

use App\Models\FinanceCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FinanceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('بيانات البند المالي')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('الكود')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('اسم البند')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('نوع البند')
                            ->options([
                                FinanceCategory::TYPE_EXPENSE => 'مصروف',
                                FinanceCategory::TYPE_INCOME => 'إيراد',
                            ])
                            ->required(),

                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}