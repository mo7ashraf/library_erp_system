<?php

namespace App\Filament\Resources\StockCountDocuments\Pages;

use App\Filament\Resources\StockCountDocuments\StockCountDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockCountDocuments extends ListRecords
{
    protected static string $resource = StockCountDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('محضر جرد جديد'),
        ];
    }
}