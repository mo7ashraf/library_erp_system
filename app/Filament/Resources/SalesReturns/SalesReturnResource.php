<?php

namespace App\Filament\Resources\SalesReturns;

use App\Filament\Resources\SalesReturns\Pages\CreateSalesReturn;
use App\Filament\Resources\SalesReturns\Pages\ListSalesReturns;
use App\Filament\Resources\SalesReturns\Pages\ViewSalesReturn;
use App\Filament\Resources\SalesReturns\Schemas\SalesReturnForm;
use App\Filament\Resources\SalesReturns\Schemas\SalesReturnInfolist;
use App\Filament\Resources\SalesReturns\Tables\SalesReturnsTable;
use App\Filament\Resources\Concerns\ProtectsPostedRecords;
use App\Models\SalesReturn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalesReturnResource extends Resource
{
    use ProtectsPostedRecords;
    protected static ?string $model = SalesReturn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static ?string $recordTitleAttribute = 'return_number';

    public static function getModelLabel(): string
    {
        return 'مرتجع مبيعات';
    }

    public static function getPluralModelLabel(): string
    {
        return 'مرتجعات المبيعات';
    }

    public static function getNavigationLabel(): string
    {
        return 'مرتجعات المبيعات';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحركات المخزنية';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return SalesReturnForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesReturnInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesReturnsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesReturns::route('/'),
            'create' => CreateSalesReturn::route('/create'),
            'view' => ViewSalesReturn::route('/{record}'),
        ];
    }
}