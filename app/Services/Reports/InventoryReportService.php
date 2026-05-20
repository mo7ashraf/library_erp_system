<?php

namespace App\Services\Reports;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\WarehouseItemBalance;
use Carbon\Carbon;

class InventoryReportService
{
    public function summary(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $this->normalizeDate($fromDate) ?: now()->startOfMonth()->toDateString();
        $toDate = $this->normalizeDate($toDate) ?: now()->toDateString();

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,

            'totals' => [
                'total_quantity' => (float) WarehouseItemBalance::query()->sum('quantity'),
                'total_value' => (float) WarehouseItemBalance::query()->sum('total_cost'),
                'items_with_stock_count' => WarehouseItemBalance::query()
                    ->where('quantity', '>', 0)
                    ->distinct('item_id')
                    ->count('item_id'),
                'warehouses_count' => WarehouseItemBalance::query()
                    ->where('quantity', '>', 0)
                    ->distinct('warehouse_id')
                    ->count('warehouse_id'),
                'zero_stock_items_count' => count($this->zeroStockItems(limit: 100000)),
                'low_stock_items_count' => count($this->lowStockItems(limit: 100000)),
            ],

            'balances_by_warehouse' => $this->balancesByWarehouse(),
            'top_value_items' => $this->topValueItems(),
            'zero_stock_items' => $this->zeroStockItems(),
            'low_stock_items' => $this->lowStockItems(),
            'movement_summary' => $this->movementSummary($fromDate, $toDate),
            'latest_movements' => $this->latestMovements($fromDate, $toDate),
        ];
    }

    private function balancesByWarehouse(): array
    {
        return WarehouseItemBalance::query()
            ->with('warehouse')
            ->selectRaw('warehouse_id, COUNT(DISTINCT item_id) as items_count, SUM(quantity) as total_quantity, SUM(total_cost) as total_value')
            ->groupBy('warehouse_id')
            ->orderByDesc('total_value')
            ->get()
            ->map(fn (WarehouseItemBalance $row): array => [
                'warehouse_id' => $row->warehouse_id,
                'warehouse_name' => $row->warehouse?->name ?? '-',
                'items_count' => (int) $row->items_count,
                'total_quantity' => (float) $row->total_quantity,
                'total_value' => (float) $row->total_value,
            ])
            ->values()
            ->toArray();
    }

    private function topValueItems(): array
    {
        return WarehouseItemBalance::query()
            ->with('item')
            ->selectRaw('item_id, SUM(quantity) as total_quantity, SUM(total_cost) as total_value')
            ->where('quantity', '>', 0)
            ->groupBy('item_id')
            ->orderByDesc('total_value')
            ->limit(20)
            ->get()
            ->map(fn (WarehouseItemBalance $row): array => [
                'item_id' => $row->item_id,
                'item_code' => $row->item?->code ?? '-',
                'item_name' => $row->item?->name ?? '-',
                'total_quantity' => (float) $row->total_quantity,
                'total_value' => (float) $row->total_value,
            ])
            ->values()
            ->toArray();
    }

    private function zeroStockItems(int $limit = 20): array
    {
        return Item::query()
            ->withSum('warehouseBalances as stock_quantity', 'quantity')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->filter(fn (Item $item): bool => (float) ($item->stock_quantity ?? 0) <= 0)
            ->take($limit)
            ->map(fn (Item $item): array => [
                'item_id' => $item->id,
                'item_code' => $item->code,
                'item_name' => $item->name,
                'stock_quantity' => (float) ($item->stock_quantity ?? 0),
                'reorder_level' => (float) ($item->reorder_level ?? 0),
                'min_stock' => (float) ($item->min_stock ?? 0),
            ])
            ->values()
            ->toArray();
    }

    private function lowStockItems(int $limit = 20): array
    {
        return Item::query()
            ->withSum('warehouseBalances as stock_quantity', 'quantity')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->filter(function (Item $item): bool {
                $quantity = (float) ($item->stock_quantity ?? 0);
                $threshold = (float) ($item->reorder_level ?: $item->min_stock ?: 0);

                return $threshold > 0 && $quantity > 0 && $quantity <= $threshold;
            })
            ->take($limit)
            ->map(function (Item $item): array {
                $threshold = (float) ($item->reorder_level ?: $item->min_stock ?: 0);

                return [
                    'item_id' => $item->id,
                    'item_code' => $item->code,
                    'item_name' => $item->name,
                    'stock_quantity' => (float) ($item->stock_quantity ?? 0),
                    'threshold' => $threshold,
                    'reorder_level' => (float) ($item->reorder_level ?? 0),
                    'min_stock' => (float) ($item->min_stock ?? 0),
                ];
            })
            ->values()
            ->toArray();
    }

    private function movementSummary(string $fromDate, string $toDate): array
    {
        return StockMovement::query()
            ->whereDate('movement_date', '>=', $fromDate)
            ->whereDate('movement_date', '<=', $toDate)
            ->selectRaw('movement_type, direction, COUNT(*) as movements_count, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
            ->groupBy('movement_type', 'direction')
            ->orderBy('movement_type')
            ->get()
            ->map(fn (StockMovement $row): array => [
                'movement_type' => $row->movement_type,
                'movement_type_label' => $this->movementTypeLabel($row->movement_type),
                'direction' => $row->direction,
                'direction_label' => $this->directionLabel($row->direction),
                'movements_count' => (int) $row->movements_count,
                'total_quantity' => (float) $row->total_quantity,
                'total_cost' => (float) $row->total_cost,
            ])
            ->values()
            ->toArray();
    }

    private function latestMovements(string $fromDate, string $toDate): array
    {
        return StockMovement::query()
            ->with(['item', 'warehouse'])
            ->whereDate('movement_date', '>=', $fromDate)
            ->whereDate('movement_date', '<=', $toDate)
            ->latest('movement_date')
            ->latest('id')
            ->limit(30)
            ->get()
            ->map(fn (StockMovement $movement): array => [
                'date' => $movement->movement_date?->format('Y-m-d') ?? '-',
                'reference_number' => $movement->reference_number ?? '-',
                'movement_type' => $this->movementTypeLabel($movement->movement_type),
                'direction' => $movement->direction,
                'direction_label' => $this->directionLabel($movement->direction),
                'item' => $movement->item?->name ?? '-',
                'warehouse' => $movement->warehouse?->name ?? '-',
                'quantity' => (float) $movement->quantity,
                'unit_cost' => (float) $movement->unit_cost,
                'total_cost' => (float) $movement->total_cost,
                'balance_after' => (float) $movement->balance_after,
            ])
            ->values()
            ->toArray();
    }

    private function movementTypeLabel(?string $state): string
    {
        return match ($state) {
            StockMovement::TYPE_OPENING_BALANCE => 'رصيد افتتاحي',
            StockMovement::TYPE_PURCHASE => 'مشتريات',
            StockMovement::TYPE_PURCHASE_RETURN => 'مرتجع مشتريات',
            StockMovement::TYPE_SALE => 'مبيعات',
            StockMovement::TYPE_SALE_RETURN => 'مرتجع مبيعات',
            StockMovement::TYPE_TRANSFER_IN => 'تحويل وارد',
            StockMovement::TYPE_TRANSFER_OUT => 'تحويل صادر',
            StockMovement::TYPE_STOCK_COUNT_INCREASE => 'تسوية جرد بالزيادة',
            StockMovement::TYPE_STOCK_COUNT_DECREASE => 'تسوية جرد بالنقص',
            StockMovement::TYPE_DAMAGED => 'تالف',
            StockMovement::TYPE_MANUAL_ADJUSTMENT => 'تسوية يدوية',
            default => $state ?: '-',
        };
    }

    private function directionLabel(?string $state): string
    {
        return match ($state) {
            StockMovement::DIRECTION_IN => 'داخل',
            StockMovement::DIRECTION_OUT => 'خارج',
            default => '-',
        };
    }

    private function normalizeDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}