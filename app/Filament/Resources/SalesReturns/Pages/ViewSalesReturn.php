<?php

namespace App\Filament\Resources\SalesReturns\Pages;

use App\Filament\Resources\SalesReturns\SalesReturnResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesReturn extends ViewRecord
{
    protected static string $resource = SalesReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('طباعة المرتجع')
                ->url(fn (): string => route('admin.prints.sales-returns.receipt', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}