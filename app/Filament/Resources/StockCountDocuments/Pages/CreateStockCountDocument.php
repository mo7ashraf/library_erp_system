<?php

namespace App\Filament\Resources\StockCountDocuments\Pages;

use App\Filament\Resources\StockCountDocuments\StockCountDocumentResource;
use App\Models\StockCountDocument;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseItemBalance;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateStockCountDocument extends CreateRecord
{
    protected static string $resource = StockCountDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $warehouse = Warehouse::find($data['warehouse_id']);

        $data['branch_id'] = $warehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = StockCountDocument::STATUS_DRAFT;
        $data['total_increase_quantity'] = 0;
        $data['total_decrease_quantity'] = 0;
        $data['total_difference_cost'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['items']);

            if ($this->record->status === StockCountDocument::STATUS_POSTED) {
                return;
            }

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $balance = WarehouseItemBalance::query()
                    ->where('warehouse_id', $this->record->warehouse_id)
                    ->where('item_id', $line->item_id)
                    ->first();

                $systemQuantity = (float) ($balance?->quantity ?? 0);
                $unitCost = (float) ($balance?->average_cost ?? 0);
                $actualQuantity = (float) $line->actual_quantity;
                $differenceQuantity = $actualQuantity - $systemQuantity;
                $differenceCost = abs($differenceQuantity) * $unitCost;

                $line->update([
                    'system_quantity' => $systemQuantity,
                    'difference_quantity' => $differenceQuantity,
                    'unit_cost' => $unitCost,
                    'difference_cost' => $differenceCost,
                ]);

                if ($differenceQuantity > 0) {
                    $stockService->increase([
                        'warehouse_id' => $this->record->warehouse_id,
                        'item_id' => $line->item_id,
                        'branch_id' => $this->record->branch_id,
                        'user_id' => auth()->id(),
                        'quantity' => $differenceQuantity,
                        'unit_cost' => $unitCost,
                        'movement_type' => StockMovement::TYPE_STOCK_COUNT_INCREASE,
                        'reference_type' => StockCountDocument::class,
                        'reference_id' => $this->record->id,
                        'reference_number' => $this->record->count_number,
                        'movement_date' => $this->record->count_date,
                        'notes' => $this->record->notes,
                    ]);
                }

                if ($differenceQuantity < 0) {
                    $stockService->decrease([
                        'warehouse_id' => $this->record->warehouse_id,
                        'item_id' => $line->item_id,
                        'branch_id' => $this->record->branch_id,
                        'user_id' => auth()->id(),
                        'quantity' => abs($differenceQuantity),
                        'unit_cost' => $unitCost,
                        'movement_type' => StockMovement::TYPE_STOCK_COUNT_DECREASE,
                        'reference_type' => StockCountDocument::class,
                        'reference_id' => $this->record->id,
                        'reference_number' => $this->record->count_number,
                        'movement_date' => $this->record->count_date,
                        'notes' => $this->record->notes,
                    ]);
                }
            }

            $this->record->refresh();
            $this->record->load(['items']);
            $this->record->recalculateTotals();

            $this->record->update([
                'status' => StockCountDocument::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}