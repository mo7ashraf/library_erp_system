<?php

namespace App\Filament\Resources\StockCountDocuments\Pages;

use App\Filament\Resources\StockCountDocuments\StockCountDocumentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewStockCountDocument extends ViewRecord
{
    protected static string $resource = StockCountDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('طباعة محضر الجرد')
                ->url(fn (): string => route('admin.prints.stock-count-documents.receipt', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}