<?php

namespace App\Filament\Resources\OpeningStockDocuments\Pages;

use App\Filament\Resources\OpeningStockDocuments\OpeningStockDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOpeningStockDocuments extends ListRecords
{
    protected static string $resource = OpeningStockDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إدخال رصيد جديد'),
        ];
    }
}