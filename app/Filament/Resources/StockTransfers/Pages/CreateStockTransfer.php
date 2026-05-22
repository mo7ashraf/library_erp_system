<?php

namespace App\Filament\Resources\StockTransfers\Pages;

use App\Filament\Resources\StockTransfers\StockTransferResource;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\WarehouseItemBalance;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateStockTransfer extends CreateRecord
{
    protected static string $resource = StockTransferResource::class;

    protected function beforeCreate(): void
    {
        $fromWarehouseId = (int) ($this->data['from_warehouse_id'] ?? 0);
        $toWarehouseId = (int) ($this->data['to_warehouse_id'] ?? 0);
        $lines = $this->data['items'] ?? [];

        if ($fromWarehouseId === $toWarehouseId) {
            throw ValidationException::withMessages([
                'data.to_warehouse_id' => 'لا يمكن التحويل إلى نفس المخزن.',
            ]);
        }

        foreach ($lines as $line) {
            $itemId = (int) ($line['item_id'] ?? 0);
            $quantity = (float) ($line['quantity'] ?? 0);

            if ($itemId <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'يجب اختيار الصنف.',
                ]);
            }

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'كمية التحويل يجب أن تكون أكبر من صفر.',
                ]);
            }

            $availableQuantity = (float) WarehouseItemBalance::query()
                ->where('warehouse_id', $fromWarehouseId)
                ->where('item_id', $itemId)
                ->value('quantity');

            if ($availableQuantity <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'هذا الصنف غير متاح في المخزن المصدر.',
                ]);
            }

            if ($quantity > $availableQuantity) {
                throw ValidationException::withMessages([
                    'data.items' => 'كمية التحويل لا يمكن أن تتجاوز المتاح في المخزن المصدر. المتاح حاليًا: '
                        . number_format($availableQuantity, 3),
                ]);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $fromWarehouse = Warehouse::find($data['from_warehouse_id']);
        $toWarehouse = Warehouse::find($data['to_warehouse_id']);

        $data['from_branch_id'] = $fromWarehouse?->branch_id;
        $data['to_branch_id'] = $toWarehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = StockTransfer::STATUS_DRAFT;
        $data['total_quantity'] = 0;
        $data['total_cost'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['items']);

            if ($this->record->status === StockTransfer::STATUS_POSTED) {
                return;
            }

            $this->record->recalculateTotals();

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $stockService->transfer([
                    'from_warehouse_id' => $this->record->from_warehouse_id,
                    'to_warehouse_id' => $this->record->to_warehouse_id,
                    'item_id' => $line->item_id,
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->unit_cost,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->transfer_number,
                    'movement_date' => $this->record->transfer_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->update([
                'status' => StockTransfer::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}