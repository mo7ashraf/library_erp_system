<?php

namespace App\Filament\Resources\ItemGroups;

use App\Filament\Resources\ItemGroups\Pages\CreateItemGroup;
use App\Filament\Resources\ItemGroups\Pages\EditItemGroup;
use App\Filament\Resources\ItemGroups\Pages\ListItemGroups;
use App\Filament\Resources\ItemGroups\Pages\ViewItemGroup;
use App\Filament\Resources\ItemGroups\Schemas\ItemGroupForm;
use App\Filament\Resources\ItemGroups\Schemas\ItemGroupInfolist;
use App\Filament\Resources\ItemGroups\Tables\ItemGroupsTable;
use App\Models\ItemGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItemGroupResource extends Resource
{
    protected static ?string $model = ItemGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'مجموعة أصناف';
    }

    public static function getPluralModelLabel(): string
    {
        return 'مجموعات الأصناف';
    }

    public static function getNavigationLabel(): string
    {
        return 'مجموعات الأصناف';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'التعريفات الرئيسية';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return ItemGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItemGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemGroupsTable::configure($table);
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
            'index' => ListItemGroups::route('/'),
            'create' => CreateItemGroup::route('/create'),
            'view' => ViewItemGroup::route('/{record}'),
            'edit' => EditItemGroup::route('/{record}/edit'),
        ];
    }
}