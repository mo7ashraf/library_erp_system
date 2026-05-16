<?php

namespace App\Filament\Resources\ItemSubgroups;

use App\Filament\Resources\ItemSubgroups\Pages\CreateItemSubgroup;
use App\Filament\Resources\ItemSubgroups\Pages\EditItemSubgroup;
use App\Filament\Resources\ItemSubgroups\Pages\ListItemSubgroups;
use App\Filament\Resources\ItemSubgroups\Pages\ViewItemSubgroup;
use App\Filament\Resources\ItemSubgroups\Schemas\ItemSubgroupForm;
use App\Filament\Resources\ItemSubgroups\Schemas\ItemSubgroupInfolist;
use App\Filament\Resources\ItemSubgroups\Tables\ItemSubgroupsTable;
use App\Models\ItemSubgroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ItemSubgroupResource extends Resource
{
    protected static ?string $model = ItemSubgroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'مجموعة فرعية';
    }

    public static function getPluralModelLabel(): string
    {
        return 'مجموعات الأصناف الفرعية';
    }

    public static function getNavigationLabel(): string
    {
        return 'مجموعات الأصناف الفرعية';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'التعريفات الرئيسية';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return ItemSubgroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ItemSubgroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemSubgroupsTable::configure($table);
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
            'index' => ListItemSubgroups::route('/'),
            'create' => CreateItemSubgroup::route('/create'),
            'view' => ViewItemSubgroup::route('/{record}'),
            'edit' => EditItemSubgroup::route('/{record}/edit'),
        ];
    }
}