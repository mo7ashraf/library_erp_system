<?php

namespace App\Filament\Resources\WarehouseItemBalances;

use App\Filament\Resources\WarehouseItemBalances\Pages\ListWarehouseItemBalances;
use App\Filament\Resources\WarehouseItemBalances\Pages\ViewWarehouseItemBalance;
use App\Filament\Resources\WarehouseItemBalances\Schemas\WarehouseItemBalanceInfolist;
use App\Filament\Resources\WarehouseItemBalances\Tables\WarehouseItemBalancesTable;
use App\Models\WarehouseItemBalance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WarehouseItemBalanceResource extends Resource
{
    protected static ?string $model = WarehouseItemBalance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return 'رصيد صنف';
    }

    public static function getPluralModelLabel(): string
    {
        return 'أرصدة الأصناف بالمخازن';
    }

    public static function getNavigationLabel(): string
    {
        return 'أرصدة الأصناف بالمخازن';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'التقارير';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function infolist(Schema $schema): Schema
    {
        return WarehouseItemBalanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehouseItemBalancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWarehouseItemBalances::route('/'),
            'view' => ViewWarehouseItemBalance::route('/{record}'),
        ];
    }
}