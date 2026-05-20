<?php

namespace App\Console\Commands;

use App\Models\StockMovement;
use App\Models\WarehouseItemBalance;
use App\Services\Reports\InventoryReportService;
use Illuminate\Console\Command;

class InventoryReportIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-inventory-report
                            {--from= : Start date in Y-m-d format}
                            {--to= : End date in Y-m-d format}';

    protected $description = 'Checks inventory summary report totals against warehouse balances and stock movements.';

    private int $errors = 0;

    public function handle(): int
    {
        $fromDate = $this->option('from') ?: now()->startOfMonth()->toDateString();
        $toDate = $this->option('to') ?: now()->toDateString();

        $this->info('Starting inventory report integrity check...');
        $this->line('------------------------------------------');
        $this->line("Period: {$fromDate} → {$toDate}");

        $report = app(InventoryReportService::class)->summary($fromDate, $toDate);

        $this->checkBalanceTotals($report);
        $this->checkWarehouseRows($report);
        $this->checkMovementSummary($report, $fromDate, $toDate);
        $this->checkRowLimits($report);

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Inventory report integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Inventory report integrity check passed.');

        return self::SUCCESS;
    }

    private function checkBalanceTotals(array $report): void
    {
        $this->info('Checking warehouse balance totals...');

        $this->assertClose(
            actual: (float) ($report['totals']['total_quantity'] ?? 0),
            expected: (float) WarehouseItemBalance::query()->sum('quantity'),
            label: 'Total stock quantity'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['total_value'] ?? 0),
            expected: (float) WarehouseItemBalance::query()->sum('total_cost'),
            label: 'Total stock value'
        );

        $this->assertEqualsInt(
            actual: (int) ($report['totals']['items_with_stock_count'] ?? 0),
            expected: WarehouseItemBalance::query()
                ->where('quantity', '>', 0)
                ->distinct('item_id')
                ->count('item_id'),
            label: 'Items with stock count'
        );
    }

    private function checkWarehouseRows(array $report): void
    {
        $this->info('Checking warehouse balance rows...');

        $rowsQuantity = collect($report['balances_by_warehouse'] ?? [])
            ->sum(fn (array $row): float => (float) ($row['total_quantity'] ?? 0));

        $rowsValue = collect($report['balances_by_warehouse'] ?? [])
            ->sum(fn (array $row): float => (float) ($row['total_value'] ?? 0));

        $this->assertClose(
            actual: (float) $rowsQuantity,
            expected: (float) WarehouseItemBalance::query()->sum('quantity'),
            label: 'Warehouse rows total quantity'
        );

        $this->assertClose(
            actual: (float) $rowsValue,
            expected: (float) WarehouseItemBalance::query()->sum('total_cost'),
            label: 'Warehouse rows total value'
        );
    }

    private function checkMovementSummary(array $report, string $fromDate, string $toDate): void
    {
        $this->info('Checking stock movement summary...');

        $expectedRows = StockMovement::query()
            ->whereDate('movement_date', '>=', $fromDate)
            ->whereDate('movement_date', '<=', $toDate)
            ->selectRaw('movement_type, direction, COUNT(*) as movements_count, SUM(quantity) as total_quantity, SUM(total_cost) as total_cost')
            ->groupBy('movement_type', 'direction')
            ->get()
            ->mapWithKeys(function (StockMovement $row): array {
                $key = $row->movement_type . '|' . $row->direction;

                return [
                    $key => [
                        'movements_count' => (int) $row->movements_count,
                        'total_quantity' => (float) $row->total_quantity,
                        'total_cost' => (float) $row->total_cost,
                    ],
                ];
            });

        $actualRows = collect($report['movement_summary'] ?? [])
            ->mapWithKeys(function (array $row): array {
                $key = ($row['movement_type'] ?? '') . '|' . ($row['direction'] ?? '');

                return [
                    $key => [
                        'movements_count' => (int) ($row['movements_count'] ?? 0),
                        'total_quantity' => (float) ($row['total_quantity'] ?? 0),
                        'total_cost' => (float) ($row['total_cost'] ?? 0),
                    ],
                ];
            });

        foreach ($expectedRows as $key => $expected) {
            if (! $actualRows->has($key)) {
                $this->errors++;
                $this->error("✗ Stock movement summary row missing: {$key}");

                continue;
            }

            $actual = $actualRows->get($key);

            $this->assertEqualsInt(
                actual: (int) $actual['movements_count'],
                expected: (int) $expected['movements_count'],
                label: "Movement count {$key}"
            );

            $this->assertClose(
                actual: (float) $actual['total_quantity'],
                expected: (float) $expected['total_quantity'],
                label: "Movement quantity {$key}"
            );

            $this->assertClose(
                actual: (float) $actual['total_cost'],
                expected: (float) $expected['total_cost'],
                label: "Movement cost {$key}"
            );
        }

        foreach ($actualRows as $key => $actual) {
            if (! $expectedRows->has($key)) {
                $this->errors++;
                $this->error("✗ Unexpected stock movement summary row: {$key}");
            }
        }
    }

    private function checkRowLimits(array $report): void
    {
        $this->info('Checking report row limits...');

        $this->assertMaxRows(count($report['top_value_items'] ?? []), 20, 'Top value items');
        $this->assertMaxRows(count($report['zero_stock_items'] ?? []), 20, 'Zero stock items');
        $this->assertMaxRows(count($report['low_stock_items'] ?? []), 20, 'Low stock items');
        $this->assertMaxRows(count($report['latest_movements'] ?? []), 30, 'Latest stock movements');
    }

    private function assertClose(float $actual, float $expected, string $label, float $tolerance = 0.01): void
    {
        if (abs($actual - $expected) > $tolerance) {
            $this->errors++;
            $this->error("✗ {$label}: expected {$expected}, actual {$actual}");

            return;
        }

        $this->line("✓ {$label}");
    }

    private function assertEqualsInt(int $actual, int $expected, string $label): void
    {
        if ($actual !== $expected) {
            $this->errors++;
            $this->error("✗ {$label}: expected {$expected}, actual {$actual}");

            return;
        }

        $this->line("✓ {$label}");
    }

    private function assertMaxRows(int $actualCount, int $maxRows, string $label): void
    {
        if ($actualCount > $maxRows) {
            $this->errors++;
            $this->error("✗ {$label}: expected max {$maxRows} rows, actual {$actualCount}");

            return;
        }

        $this->line("✓ {$label} row limit");
    }
}