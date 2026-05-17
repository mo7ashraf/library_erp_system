<?php

namespace App\Filament\Resources\Cashboxes\Pages;

use App\Filament\Resources\Cashboxes\CashboxResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCashbox extends ViewRecord
{
    protected static string $resource = CashboxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
