<?php

namespace App\Filament\Resources\DamagedStockDocuments\Pages;

use App\Filament\Resources\DamagedStockDocuments\DamagedStockDocumentResource;
use App\Models\DamagedStockDocument;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseItemBalance;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateDamagedStockDocument extends CreateRecord
{
    protected static string $resource = DamagedStockDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $warehouse = Warehouse::find($data['warehouse_id']);

        $data['branch_id'] = $warehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = DamagedStockDocument::STATUS_DRAFT;
        $data['total_quantity'] = 0;
        $data['total_cost'] = 0;

        return $data;
    }

    private function validateAvailableQuantities(): void
    {
        $items = $this->record->items
            ->groupBy('item_id')
            ->map(fn ($lines) => (float) $lines->sum('quantity'));

        foreach ($items as $itemId => $requiredQty) {
            $availableQty = (float) WarehouseItemBalance::query()
                ->where('warehouse_id', $this->record->warehouse_id)
                ->where('item_id', $itemId)
                ->value('quantity');

            if ($requiredQty > $availableQty) {
                throw ValidationException::withMessages([
                    'items' => "لا يمكن إخراج كمية {$requiredQty}. الرصيد المتاح لهذا الصنف هو {$availableQty} فقط.",
                ]);
            }
        }
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['items']);

            if ($this->record->status === DamagedStockDocument::STATUS_POSTED) {
                return;
            }

            $this->validateAvailableQuantities();

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $balance = WarehouseItemBalance::query()
                    ->where('warehouse_id', $this->record->warehouse_id)
                    ->where('item_id', $line->item_id)
                    ->first();

                $unitCost = (float) ($balance?->average_cost ?? 0);

                $line->update([
                    'unit_cost' => $unitCost,
                    'total_cost' => (float) $line->quantity * $unitCost,
                ]);

                $stockService->decrease([
                    'warehouse_id' => $this->record->warehouse_id,
                    'item_id' => $line->item_id,
                    'branch_id' => $this->record->branch_id,
                    'user_id' => auth()->id(),
                    'quantity' => $line->quantity,
                    'unit_cost' => $unitCost,
                    'movement_type' => StockMovement::TYPE_DAMAGED,
                    'reference_type' => DamagedStockDocument::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->document_number,
                    'movement_date' => $this->record->document_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->refresh();
            $this->record->load(['items']);
            $this->record->recalculateTotals();

            $this->record->update([
                'status' => DamagedStockDocument::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}