<?php

namespace App\Services\Inventory;

use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseItemBalance;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function increase(array $data): StockMovement
    {
        return $this->recordMovement($data, StockMovement::DIRECTION_IN);
    }

    public function decrease(array $data): StockMovement
    {
        return $this->recordMovement($data, StockMovement::DIRECTION_OUT);
    }

    public function recordMovement(array $data, string $direction): StockMovement
    {
        return DB::transaction(function () use ($data, $direction) {
            $warehouseId = (int) $data['warehouse_id'];
            $itemId = (int) $data['item_id'];
            $quantity = (float) $data['quantity'];
            $unitCost = (float) ($data['unit_cost'] ?? 0);

            if ($quantity <= 0) {
                throw new RuntimeException('الكمية يجب أن تكون أكبر من صفر.');
            }

            $balance = WarehouseItemBalance::firstOrCreate(
                [
                    'warehouse_id' => $warehouseId,
                    'item_id' => $itemId,
                ],
                [
                    'quantity' => 0,
                    'average_cost' => 0,
                    'total_cost' => 0,
                ]
            );

            $currentQuantity = (float) $balance->quantity;
            $currentTotalCost = (float) $balance->total_cost;

            if ($direction === StockMovement::DIRECTION_OUT && $currentQuantity < $quantity) {
                throw new RuntimeException('لا توجد كمية كافية في المخزن لإتمام الحركة.');
            }

            $movementTotalCost = $quantity * $unitCost;

            if ($direction === StockMovement::DIRECTION_IN) {
                $newQuantity = $currentQuantity + $quantity;
                $newTotalCost = $currentTotalCost + $movementTotalCost;
            } else {
                $newQuantity = $currentQuantity - $quantity;

                $costToRemove = $quantity * (float) $balance->average_cost;
                $newTotalCost = max(0, $currentTotalCost - $costToRemove);
            }

            $newAverageCost = $newQuantity > 0 ? $newTotalCost / $newQuantity : 0;

            $balance->update([
                'quantity' => $newQuantity,
                'average_cost' => $newAverageCost,
                'total_cost' => $newTotalCost,
            ]);

            $warehouse = Warehouse::find($warehouseId);

            return StockMovement::create([
                'warehouse_id' => $warehouseId,
                'item_id' => $itemId,
                'branch_id' => $data['branch_id'] ?? $warehouse?->branch_id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'movement_type' => $data['movement_type'],
                'direction' => $direction,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $movementTotalCost,
                'balance_after' => $newQuantity,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function currentBalance(int $warehouseId, int $itemId): float
    {
        return (float) WarehouseItemBalance::query()
            ->where('warehouse_id', $warehouseId)
            ->where('item_id', $itemId)
            ->value('quantity');
    }

    public function transfer(array $data): void
    {
        DB::transaction(function () use ($data) {
            $this->decrease([
                'warehouse_id' => $data['from_warehouse_id'],
                'item_id' => $data['item_id'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'] ?? 0,
                'movement_type' => StockMovement::TYPE_TRANSFER_OUT,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->increase([
                'warehouse_id' => $data['to_warehouse_id'],
                'item_id' => $data['item_id'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'] ?? 0,
                'movement_type' => StockMovement::TYPE_TRANSFER_IN,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}