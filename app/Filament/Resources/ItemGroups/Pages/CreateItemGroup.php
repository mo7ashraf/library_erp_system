<?php

namespace App\Filament\Resources\ItemGroups\Pages;

use App\Filament\Resources\ItemGroups\ItemGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateItemGroup extends CreateRecord
{
    protected static string $resource = ItemGroupResource::class;
}
