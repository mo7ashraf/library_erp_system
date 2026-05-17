<?php

namespace App\Filament\Resources\Cashboxes\Pages;

use App\Filament\Resources\Cashboxes\CashboxResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCashboxes extends ListRecords
{
    protected static string $resource = CashboxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
