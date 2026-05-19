<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\TreasuryTransaction;
use App\Models\WarehouseItemBalance;
use App\Services\Dashboard\ErpDashboardService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DashboardIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-dashboard';

    protected $description = 'Checks ERP dashboard KPI totals against database records.';

    private int $errors = 0;

    public function handle(): int
    {
        $this->info('Starting ERP dashboard integrity check...');
        $this->line('------------------------------------------');

        $dashboard = app(ErpDashboardService::class)->summary();

        $today = Carbon::today()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();

        $this->checkSales($dashboard, $today, $monthStart, $monthEnd);
        $this->checkPurchases($dashboard, $today, $monthStart, $monthEnd);
        $this->checkTreasury($dashboard, $today, $monthStart, $monthEnd);
        $this->checkBalances($dashboard);
        $this->checkInventory($dashboard);
        $this->checkLatestBlocks($dashboard);

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("ERP dashboard integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ ERP dashboard integrity check passed.');

        return self::SUCCESS;
    }

    private function checkSales(array $dashboard, string $today, string $monthStart, string $monthEnd): void
    {
        $this->info('Checking sales dashboard KPIs...');

        $expectedTodayTotal = (float) SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', $today)
            ->sum('grand_total');

        $expectedTodayCount = SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', $today)
            ->count();

        $expectedMonthTotal = (float) SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $monthStart)
            ->whereDate('invoice_date', '<=', $monthEnd)
            ->sum('grand_total');

        $expectedMonthCount = SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $monthStart)
            ->whereDate('invoice_date', '<=', $monthEnd)
            ->count();

        $actual = $dashboard['sales'] ?? [];

        $this->assertClose((float) ($actual['today_total'] ?? 0), $expectedTodayTotal, 'Today sales total');
        $this->assertEqualsInt((int) ($actual['today_count'] ?? 0), $expectedTodayCount, 'Today sales count');

        $this->assertClose((float) ($actual['month_total'] ?? 0), $expectedMonthTotal, 'Month sales total');
        $this->assertEqualsInt((int) ($actual['month_count'] ?? 0), $expectedMonthCount, 'Month sales count');
    }

    private function checkPurchases(array $dashboard, string $today, string $monthStart, string $monthEnd): void
    {
        $this->info('Checking purchase dashboard KPIs...');

        $expectedTodayTotal = (float) PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', $today)
            ->sum('grand_total');

        $expectedTodayCount = PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', $today)
            ->count();

        $expectedMonthTotal = (float) PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $monthStart)
            ->whereDate('invoice_date', '<=', $monthEnd)
            ->sum('grand_total');

        $expectedMonthCount = PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $monthStart)
            ->whereDate('invoice_date', '<=', $monthEnd)
            ->count();

        $actual = $dashboard['purchases'] ?? [];

        $this->assertClose((float) ($actual['today_total'] ?? 0), $expectedTodayTotal, 'Today purchase total');
        $this->assertEqualsInt((int) ($actual['today_count'] ?? 0), $expectedTodayCount, 'Today purchase count');

        $this->assertClose((float) ($actual['month_total'] ?? 0), $expectedMonthTotal, 'Month purchase total');
        $this->assertEqualsInt((int) ($actual['month_count'] ?? 0), $expectedMonthCount, 'Month purchase count');
    }

    private function checkTreasury(array $dashboard, string $today, string $monthStart, string $monthEnd): void
    {
        $this->info('Checking treasury dashboard KPIs...');

        $expectedTodayInflow = $this->treasurySum(TreasuryTransaction::DIRECTION_IN, $today, $today);
        $expectedTodayOutflow = $this->treasurySum(TreasuryTransaction::DIRECTION_OUT, $today, $today);

        $expectedMonthInflow = $this->treasurySum(TreasuryTransaction::DIRECTION_IN, $monthStart, $monthEnd);
        $expectedMonthOutflow = $this->treasurySum(TreasuryTransaction::DIRECTION_OUT, $monthStart, $monthEnd);

        $actual = $dashboard['treasury'] ?? [];

        $this->assertClose((float) ($actual['today_inflow'] ?? 0), $expectedTodayInflow, 'Today treasury inflow');
        $this->assertClose((float) ($actual['today_outflow'] ?? 0), $expectedTodayOutflow, 'Today treasury outflow');

        $this->assertClose((float) ($actual['month_inflow'] ?? 0), $expectedMonthInflow, 'Month treasury inflow');
        $this->assertClose((float) ($actual['month_outflow'] ?? 0), $expectedMonthOutflow, 'Month treasury outflow');
    }

    private function checkBalances(array $dashboard): void
    {
        $this->info('Checking cashbox and bank balances...');

        $expectedCashboxBalance = (float) Cashbox::query()->sum('current_balance');
        $expectedBankBalance = (float) BankAccount::query()->sum('current_balance');

        $actual = $dashboard['treasury'] ?? [];

        $this->assertClose((float) ($actual['cashbox_balance'] ?? 0), $expectedCashboxBalance, 'Dashboard cashbox balance');
        $this->assertClose((float) ($actual['bank_balance'] ?? 0), $expectedBankBalance, 'Dashboard bank balance');
    }

    private function checkInventory(array $dashboard): void
    {
        $this->info('Checking inventory dashboard KPIs...');

        $expectedQuantity = (float) WarehouseItemBalance::query()->sum('quantity');
        $expectedValue = (float) WarehouseItemBalance::query()->sum('total_cost');

        $expectedItemsCount = WarehouseItemBalance::query()
            ->where('quantity', '>', 0)
            ->distinct('item_id')
            ->count('item_id');

        $actual = $dashboard['inventory'] ?? [];

        $this->assertClose((float) ($actual['total_quantity'] ?? 0), $expectedQuantity, 'Inventory total quantity');
        $this->assertClose((float) ($actual['total_value'] ?? 0), $expectedValue, 'Inventory total value');
        $this->assertEqualsInt((int) ($actual['items_count'] ?? 0), $expectedItemsCount, 'Inventory items count');
    }

    private function checkLatestBlocks(array $dashboard): void
    {
        $this->info('Checking dashboard latest blocks limits...');

        $salesLatest = $dashboard['sales']['latest'] ?? [];
        $purchaseLatest = $dashboard['purchases']['latest'] ?? [];
        $treasuryLatest = $dashboard['treasury']['latest'] ?? [];
        $topInventoryItems = $dashboard['inventory']['top_value_items'] ?? [];

        $this->assertMaxRows(count($salesLatest), 5, 'Latest sales invoices');
        $this->assertMaxRows(count($purchaseLatest), 5, 'Latest purchase invoices');
        $this->assertMaxRows(count($treasuryLatest), 8, 'Latest treasury transactions');
        $this->assertMaxRows(count($topInventoryItems), 8, 'Top inventory value items');
    }

    private function treasurySum(string $direction, string $fromDate, string $toDate): float
    {
        return (float) TreasuryTransaction::query()
            ->where('direction', $direction)
            ->whereDate('transaction_date', '>=', $fromDate)
            ->whereDate('transaction_date', '<=', $toDate)
            ->sum('amount');
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