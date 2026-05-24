<?php

namespace App\Filament\Resources\StockCountDocuments;

use App\Filament\Resources\StockCountDocuments\Pages\CreateStockCountDocument;
use App\Filament\Resources\StockCountDocuments\Pages\ListStockCountDocuments;
use App\Filament\Resources\StockCountDocuments\Pages\ViewStockCountDocument;
use App\Filament\Resources\StockCountDocuments\Schemas\StockCountDocumentForm;
use App\Filament\Resources\StockCountDocuments\Schemas\StockCountDocumentInfolist;
use App\Filament\Resources\StockCountDocuments\Tables\StockCountDocumentsTable;
use App\Filament\Resources\Concerns\ProtectsPostedRecords;
use App\Models\StockCountDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockCountDocumentResource extends Resource
{
    use ProtectsPostedRecords;
    protected static ?string $model = StockCountDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'count_number';

    public static function getModelLabel(): string
    {
        return 'محضر جرد';
    }

    public static function getPluralModelLabel(): string
    {
        return 'محاضر الجرد';
    }

    public static function getNavigationLabel(): string
    {
        return 'محضر جرد مخزن';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحركات المخزنية';
    }

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function form(Schema $schema): Schema
    {
        return StockCountDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockCountDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockCountDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockCountDocuments::route('/'),
            'create' => CreateStockCountDocument::route('/create'),
            'view' => ViewStockCountDocument::route('/{record}'),
        ];
    }
}