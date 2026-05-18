<?php

namespace App\Filament\Resources\PaymentVouchers\Pages;

use App\Filament\Resources\PaymentVouchers\PaymentVoucherResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentVoucher extends ViewRecord
{
    protected static string $resource = PaymentVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('طباعة سند الصرف')
                ->url(fn (): string => route('admin.prints.payment-vouchers.receipt', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}