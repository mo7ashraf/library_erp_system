<?php

namespace App\Filament\Resources\ItemSubgroups\Pages;

use App\Filament\Resources\ItemSubgroups\ItemSubgroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewItemSubgroup extends ViewRecord
{
    protected static string $resource = ItemSubgroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
