<?php

namespace App\Filament\Resources\DamagedStockDocuments\Pages;

use App\Filament\Resources\DamagedStockDocuments\DamagedStockDocumentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewDamagedStockDocument extends ViewRecord
{
    protected static string $resource = DamagedStockDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('طباعة الإذن')
                ->url(fn (): string => route('admin.prints.damaged-stock-documents.receipt', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}