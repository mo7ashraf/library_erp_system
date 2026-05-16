<?php

namespace App\Filament\Resources\ItemGroups\Pages;

use App\Filament\Resources\ItemGroups\ItemGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewItemGroup extends ViewRecord
{
    protected static string $resource = ItemGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
