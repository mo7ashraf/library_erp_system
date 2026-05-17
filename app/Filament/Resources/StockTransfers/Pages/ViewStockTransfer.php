<?php

namespace App\Filament\Resources\StockTransfers\Pages;

use App\Filament\Resources\StockTransfers\StockTransferResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewStockTransfer extends ViewRecord
{
    protected static string $resource = StockTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('طباعة إذن التحويل')
                ->url(fn (): string => route('admin.prints.stock-transfers.receipt', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}