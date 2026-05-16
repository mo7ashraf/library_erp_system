<?php

namespace App\Filament\Resources\WarehouseItemBalances\Pages;

use App\Filament\Resources\WarehouseItemBalances\WarehouseItemBalanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWarehouseItemBalance extends ViewRecord
{
    protected static string $resource = WarehouseItemBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
