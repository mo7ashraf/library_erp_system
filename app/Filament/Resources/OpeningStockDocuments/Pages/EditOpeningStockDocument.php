<?php

namespace App\Filament\Resources\OpeningStockDocuments\Pages;

use App\Filament\Resources\OpeningStockDocuments\OpeningStockDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOpeningStockDocument extends EditRecord
{
    protected static string $resource = OpeningStockDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
