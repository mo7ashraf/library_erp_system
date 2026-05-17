<?php

namespace App\Filament\Resources\Cashboxes;

use App\Filament\Resources\Cashboxes\Pages\CreateCashbox;
use App\Filament\Resources\Cashboxes\Pages\EditCashbox;
use App\Filament\Resources\Cashboxes\Pages\ListCashboxes;
use App\Filament\Resources\Cashboxes\Pages\ViewCashbox;
use App\Filament\Resources\Cashboxes\Schemas\CashboxForm;
use App\Filament\Resources\Cashboxes\Schemas\CashboxInfolist;
use App\Filament\Resources\Cashboxes\Tables\CashboxesTable;
use App\Models\Cashbox;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CashboxResource extends Resource
{
    protected static ?string $model = Cashbox::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CashboxForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CashboxInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashboxesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCashboxes::route('/'),
            'create' => CreateCashbox::route('/create'),
            'view' => ViewCashbox::route('/{record}'),
            'edit' => EditCashbox::route('/{record}/edit'),
        ];
    }
    public static function getModelLabel(): string
    {
        return 'خزينة';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الخزائن';
    }

    public static function getNavigationLabel(): string
    {
        return 'الخزائن';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحسابات والمالية';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
