<?php

namespace App\Filament\Resources\ItemSubgroups\Pages;

use App\Filament\Resources\ItemSubgroups\ItemSubgroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemSubgroups extends ListRecords
{
    protected static string $resource = ItemSubgroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
