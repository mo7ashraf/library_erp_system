<?php

namespace App\Filament\Resources\WarehouseItemBalances\Pages;

use App\Filament\Resources\WarehouseItemBalances\WarehouseItemBalanceResource;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseItemBalances extends ListRecords
{
    protected static string $resource = WarehouseItemBalanceResource::class;
}