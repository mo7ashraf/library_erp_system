<?php

namespace App\Filament\Resources\StockMovements;

use App\Filament\Resources\StockMovements\Pages\ListStockMovements;
use App\Filament\Resources\StockMovements\Pages\ViewStockMovement;
use App\Filament\Resources\StockMovements\Schemas\StockMovementInfolist;
use App\Filament\Resources\StockMovements\Tables\StockMovementsTable;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'reference_number';

    public static function getModelLabel(): string
    {
        return 'حركة مخزون';
    }

    public static function getPluralModelLabel(): string
    {
        return 'حركات المخزون';
    }

    public static function getNavigationLabel(): string
    {
        return 'حركة صنف تفصيلي';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'التقارير';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockMovementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'view' => ViewStockMovement::route('/{record}'),
        ];
    }
}