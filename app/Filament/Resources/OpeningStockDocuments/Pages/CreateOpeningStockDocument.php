<?php

namespace App\Filament\Resources\OpeningStockDocuments\Pages;

use App\Filament\Resources\OpeningStockDocuments\OpeningStockDocumentResource;
use App\Models\OpeningStockDocument;
use App\Models\StockMovement;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateOpeningStockDocument extends CreateRecord
{
    protected static string $resource = OpeningStockDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $warehouse = \App\Models\Warehouse::find($data['warehouse_id']);

        $data['branch_id'] = $warehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = OpeningStockDocument::STATUS_DRAFT;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['warehouse', 'items']);

            if ($this->record->status === OpeningStockDocument::STATUS_POSTED) {
                return;
            }

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $stockService->increase([
                    'warehouse_id' => $this->record->warehouse_id,
                    'item_id' => $line->item_id,
                    'branch_id' => $this->record->branch_id,
                    'user_id' => auth()->id(),
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'movement_type' => StockMovement::TYPE_OPENING_BALANCE,
                    'reference_type' => OpeningStockDocument::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->reference_number,
                    'movement_date' => $this->record->document_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->update([
                'status' => OpeningStockDocument::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}