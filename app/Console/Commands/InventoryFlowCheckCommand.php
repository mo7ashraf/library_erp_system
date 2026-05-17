<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\Inventory\StockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class InventoryFlowCheckCommand extends Command
{
    protected $signature = 'inventory:check-flow';

    protected $description = 'Safely checks core inventory item processing using a rollback transaction.';

    public function handle(): int
    {
        $this->info('Starting inventory processing check...');

        DB::beginTransaction();

        try {
            $suffix = now()->format('YmdHis');

            $branch = Branch::create([
                'code' => "TEST-BR-{$suffix}",
                'name' => 'فرع اختبار المخزون',
                'is_active' => true,
            ]);

            $fromWarehouse = Warehouse::create([
                'branch_id' => $branch->id,
                'code' => "TEST-WH-FROM-{$suffix}",
                'name' => 'مخزن اختبار صادر',
                'is_active' => true,
            ]);

            $toWarehouse = Warehouse::create([
                'branch_id' => $branch->id,
                'code' => "TEST-WH-TO-{$suffix}",
                'name' => 'مخزن اختبار وارد',
                'is_active' => true,
            ]);

            $unit = Unit::create([
                'code' => "TEST-UNIT-{$suffix}",
                'name' => 'قطعة اختبار',
                'symbol' => 'pcs',
                'is_active' => true,
            ]);

            $group = ItemGroup::create([
                'code' => "TEST-GRP-{$suffix}",
                'name' => 'مجموعة اختبار',
                'is_active' => true,
            ]);

            $item = Item::create([
                'item_group_id' => $group->id,
                'base_unit_id' => $unit->id,
                'code' => "TEST-ITEM-{$suffix}",
                'barcode' => "TEST-BARCODE-{$suffix}",
                'name' => 'صنف اختبار معالجة المخزون',
                'purchase_price' => 10,
                'student_price' => 15,
                'teacher_price' => 14,
                'representative_price' => 13,
                'retail_price' => 15,
                'wholesale_price' => 12,
                'continue_balance' => true,
                'is_active' => true,
            ]);

            $stockService = app(StockService::class);

            // 1. Opening balance: +10
            $stockService->increase([
                'warehouse_id' => $fromWarehouse->id,
                'item_id' => $item->id,
                'quantity' => 10,
                'unit_cost' => 8,
                'movement_type' => StockMovement::TYPE_OPENING_BALANCE,
                'reference_number' => "TEST-OPEN-{$suffix}",
                'movement_date' => now()->toDateString(),
            ]);

            $this->assertBalance($stockService, $fromWarehouse->id, $item->id, 10, 'Opening balance');

            // 2. Sale: -3
            $stockService->decrease([
                'warehouse_id' => $fromWarehouse->id,
                'item_id' => $item->id,
                'quantity' => 3,
                'unit_cost' => 8,
                'movement_type' => StockMovement::TYPE_SALE,
                'reference_number' => "TEST-SALE-{$suffix}",
                'movement_date' => now()->toDateString(),
            ]);

            $this->assertBalance($stockService, $fromWarehouse->id, $item->id, 7, 'Sale decrease');

            // 3. Prevent overselling/outgoing over available quantity
            try {
                $stockService->decrease([
                    'warehouse_id' => $fromWarehouse->id,
                    'item_id' => $item->id,
                    'quantity' => 20,
                    'unit_cost' => 8,
                    'movement_type' => StockMovement::TYPE_DAMAGED,
                    'reference_number' => "TEST-OVER-{$suffix}",
                    'movement_date' => now()->toDateString(),
                ]);

                throw new RuntimeException('Oversell/outgoing validation failed. System allowed quantity greater than balance.');
            } catch (RuntimeException $exception) {
                $this->info('✓ Over-quantity outgoing movement correctly rejected.');
            }

            $this->assertBalance($stockService, $fromWarehouse->id, $item->id, 7, 'Balance after rejected outgoing');

            // 4. Transfer: -2 from first warehouse, +2 to second warehouse
            $stockService->transfer([
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'item_id' => $item->id,
                'quantity' => 2,
                'unit_cost' => 8,
                'reference_number' => "TEST-TRN-{$suffix}",
                'movement_date' => now()->toDateString(),
            ]);

            $this->assertBalance($stockService, $fromWarehouse->id, $item->id, 5, 'Transfer out');
            $this->assertBalance($stockService, $toWarehouse->id, $item->id, 2, 'Transfer in');

            // 5. Sales return: +1
            $stockService->increase([
                'warehouse_id' => $fromWarehouse->id,
                'item_id' => $item->id,
                'quantity' => 1,
                'unit_cost' => 8,
                'movement_type' => StockMovement::TYPE_SALE_RETURN,
                'reference_number' => "TEST-SRET-{$suffix}",
                'movement_date' => now()->toDateString(),
            ]);

            $this->assertBalance($stockService, $fromWarehouse->id, $item->id, 6, 'Sales return increase');

            // 6. Damaged stock: -1
            $stockService->decrease([
                'warehouse_id' => $fromWarehouse->id,
                'item_id' => $item->id,
                'quantity' => 1,
                'unit_cost' => 8,
                'movement_type' => StockMovement::TYPE_DAMAGED,
                'reference_number' => "TEST-DMG-{$suffix}",
                'movement_date' => now()->toDateString(),
            ]);

            $this->assertBalance($stockService, $fromWarehouse->id, $item->id, 5, 'Damaged stock decrease');

            DB::rollBack();

            $this->info('------------------------------------------');
            $this->info('✓ Inventory processing check passed.');
            $this->info('✓ All test data was rolled back.');
            $this->info('------------------------------------------');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->error('Inventory processing check failed:');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function assertBalance(StockService $stockService, int $warehouseId, int $itemId, float $expected, string $step): void
    {
        $actual = $stockService->currentBalance($warehouseId, $itemId);

        if (abs($actual - $expected) > 0.0001) {
            throw new RuntimeException("{$step} failed. Expected balance {$expected}, actual balance {$actual}.");
        }

        $this->info("✓ {$step}: balance = {$actual}");
    }
}