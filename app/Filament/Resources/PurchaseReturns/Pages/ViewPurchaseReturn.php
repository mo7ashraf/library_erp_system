<?php

namespace App\Filament\Resources\PurchaseReturns\Pages;

use App\Filament\Resources\PurchaseReturns\PurchaseReturnResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseReturn extends ViewRecord
{
    protected static string $resource = PurchaseReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('طباعة المرتجع')
                ->url(fn (): string => route('admin.prints.purchase-returns.receipt', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}