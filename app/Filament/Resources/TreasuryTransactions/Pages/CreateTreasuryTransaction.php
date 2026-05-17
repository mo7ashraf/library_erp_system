<?php

namespace App\Filament\Resources\TreasuryTransactions\Pages;

use App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTreasuryTransaction extends CreateRecord
{
    protected static string $resource = TreasuryTransactionResource::class;
}
