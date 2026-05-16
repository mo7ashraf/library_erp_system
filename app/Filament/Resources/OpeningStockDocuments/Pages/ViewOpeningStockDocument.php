<?php

namespace App\Filament\Resources\OpeningStockDocuments\Pages;

use App\Filament\Resources\OpeningStockDocuments\OpeningStockDocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOpeningStockDocument extends ViewRecord
{
    protected static string $resource = OpeningStockDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
