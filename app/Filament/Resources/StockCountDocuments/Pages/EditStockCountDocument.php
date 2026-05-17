<?php

namespace App\Filament\Resources\StockCountDocuments\Pages;

use App\Filament\Resources\StockCountDocuments\StockCountDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStockCountDocument extends EditRecord
{
    protected static string $resource = StockCountDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
