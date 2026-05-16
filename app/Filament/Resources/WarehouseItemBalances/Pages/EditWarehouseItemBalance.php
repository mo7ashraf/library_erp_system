<?php

namespace App\Filament\Resources\WarehouseItemBalances\Pages;

use App\Filament\Resources\WarehouseItemBalances\WarehouseItemBalanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWarehouseItemBalance extends EditRecord
{
    protected static string $resource = WarehouseItemBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
