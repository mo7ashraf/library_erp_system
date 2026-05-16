<?php

namespace App\Filament\Resources\OpeningStockDocuments;

use App\Filament\Resources\OpeningStockDocuments\Pages\CreateOpeningStockDocument;
use App\Filament\Resources\OpeningStockDocuments\Pages\ListOpeningStockDocuments;
use App\Filament\Resources\OpeningStockDocuments\Pages\ViewOpeningStockDocument;
use App\Filament\Resources\OpeningStockDocuments\Schemas\OpeningStockDocumentForm;
use App\Filament\Resources\OpeningStockDocuments\Schemas\OpeningStockDocumentInfolist;
use App\Filament\Resources\OpeningStockDocuments\Tables\OpeningStockDocumentsTable;
use App\Models\OpeningStockDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OpeningStockDocumentResource extends Resource
{
    protected static ?string $model = OpeningStockDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxArrowDown;

    protected static ?string $recordTitleAttribute = 'reference_number';

    public static function getModelLabel(): string
    {
        return 'رصيد أول المدة';
    }

    public static function getPluralModelLabel(): string
    {
        return 'إدخال رصيد أول المدة';
    }

    public static function getNavigationLabel(): string
    {
        return 'إدخال رصيد أول المدة';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحركات المخزنية';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return OpeningStockDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OpeningStockDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpeningStockDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOpeningStockDocuments::route('/'),
            'create' => CreateOpeningStockDocument::route('/create'),
            'view' => ViewOpeningStockDocument::route('/{record}'),
        ];
    }
}