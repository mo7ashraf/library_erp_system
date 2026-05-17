<?php

namespace App\Filament\Resources\DamagedStockDocuments;

use App\Filament\Resources\DamagedStockDocuments\Pages\CreateDamagedStockDocument;
use App\Filament\Resources\DamagedStockDocuments\Pages\ListDamagedStockDocuments;
use App\Filament\Resources\DamagedStockDocuments\Pages\ViewDamagedStockDocument;
use App\Filament\Resources\DamagedStockDocuments\Schemas\DamagedStockDocumentForm;
use App\Filament\Resources\DamagedStockDocuments\Schemas\DamagedStockDocumentInfolist;
use App\Filament\Resources\DamagedStockDocuments\Tables\DamagedStockDocumentsTable;
use App\Models\DamagedStockDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DamagedStockDocumentResource extends Resource
{
    protected static ?string $model = DamagedStockDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrash;

    protected static ?string $recordTitleAttribute = 'document_number';

    public static function getModelLabel(): string
    {
        return 'إذن تالف / هالك';
    }

    public static function getPluralModelLabel(): string
    {
        return 'أذون التالف والهالك';
    }

    public static function getNavigationLabel(): string
    {
        return 'إذن تالف / هالك';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الحركات المخزنية';
    }

    public static function getNavigationSort(): ?int
    {
        return 8;
    }

    public static function form(Schema $schema): Schema
    {
        return DamagedStockDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DamagedStockDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DamagedStockDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDamagedStockDocuments::route('/'),
            'create' => CreateDamagedStockDocument::route('/create'),
            'view' => ViewDamagedStockDocument::route('/{record}'),
        ];
    }
}