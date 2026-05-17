<?php

namespace App\Filament\Resources\DamagedStockDocuments\Pages;

use App\Filament\Resources\DamagedStockDocuments\DamagedStockDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDamagedStockDocument extends EditRecord
{
    protected static string $resource = DamagedStockDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
