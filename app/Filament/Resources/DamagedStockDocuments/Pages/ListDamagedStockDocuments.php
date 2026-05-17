<?php

namespace App\Filament\Resources\DamagedStockDocuments\Pages;

use App\Filament\Resources\DamagedStockDocuments\DamagedStockDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDamagedStockDocuments extends ListRecords
{
    protected static string $resource = DamagedStockDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إذن جديد'),
        ];
    }
}