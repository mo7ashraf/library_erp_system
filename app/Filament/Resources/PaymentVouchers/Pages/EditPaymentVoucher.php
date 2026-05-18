<?php

namespace App\Filament\Resources\PaymentVouchers\Pages;

use App\Filament\Resources\PaymentVouchers\PaymentVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentVoucher extends EditRecord
{
    protected static string $resource = PaymentVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
