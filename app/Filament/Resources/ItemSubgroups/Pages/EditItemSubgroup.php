<?php

namespace App\Filament\Resources\ItemSubgroups\Pages;

use App\Filament\Resources\ItemSubgroups\ItemSubgroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditItemSubgroup extends EditRecord
{
    protected static string $resource = ItemSubgroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
