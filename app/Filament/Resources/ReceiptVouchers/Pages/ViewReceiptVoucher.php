<?php

namespace App\Filament\Resources\ReceiptVouchers\Pages;

use App\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewReceiptVoucher extends ViewRecord
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('طباعة سند القبض')
                ->url(fn (): string => route('admin.prints.receipt-vouchers.receipt', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}