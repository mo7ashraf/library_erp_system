<?php

namespace App\Filament\Resources\FinanceCategories;

use App\Filament\Resources\FinanceCategories\Pages\CreateFinanceCategory;
use App\Filament\Resources\FinanceCategories\Pages\ListFinanceCategories;
use App\Filament\Resources\FinanceCategories\Pages\ViewFinanceCategory;
use App\Filament\Resources\FinanceCategories\Schemas\FinanceCategoryForm;
use App\Filament\Resources\FinanceCategories\Schemas\FinanceCategoryInfolist;
use App\Filament\Resources\FinanceCategories\Tables\FinanceCategoriesTable;
use App\Models\FinanceCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FinanceCategoryResource extends Resource
{
    protected static ?string $model = FinanceCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'بند مالي';
    }

    public static function getPluralModelLabel(): string
    {
        return 'بنود مالية';
    }

    public static function getNavigationLabel(): string
    {
        return 'بنود مالية';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحسابات والمالية';
    }

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function form(Schema $schema): Schema
    {
        return FinanceCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FinanceCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceCategories::route('/'),
            'create' => CreateFinanceCategory::route('/create'),
            'view' => ViewFinanceCategory::route('/{record}'),
        ];
    }
}