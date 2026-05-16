<?php

namespace App\Filament\Resources\ItemGroups\Pages;

use App\Filament\Resources\ItemGroups\ItemGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItemGroups extends ListRecords
{
    protected static string $resource = ItemGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
