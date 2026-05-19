<?php

namespace App\Console\Commands;

use App\Models\PurchaseInvoice;
use App\Services\Reports\PurchaseReportService;
use Illuminate\Console\Command;

class PurchaseReportIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-purchase-report
                            {--from= : Start date in Y-m-d format}
                            {--to= : End date in Y-m-d format}';

    protected $description = 'Checks purchase summary report totals against posted purchase invoices.';

    private int $errors = 0;

    public function handle(): int
    {
        $fromDate = $this->option('from') ?: now()->startOfMonth()->toDateString();
        $toDate = $this->option('to') ?: now()->toDateString();

        $this->info('Starting purchase report integrity check...');
        $this->line('------------------------------------------');
        $this->line("Period: {$fromDate} → {$toDate}");

        $report = app(PurchaseReportService::class)->summary($fromDate, $toDate);

        $baseQuery = PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate);

        $this->assertEqualsInt(
            actual: (int) ($report['totals']['invoices_count'] ?? 0),
            expected: (clone $baseQuery)->count(),
            label: 'Purchase invoices count'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['subtotal'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('subtotal'),
            label: 'Purchase subtotal'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['discount_amount'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('discount_amount'),
            label: 'Purchase discount'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['additional_cost'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('additional_cost'),
            label: 'Purchase additional cost'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['grand_total'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('grand_total'),
            label: 'Purchase grand total'
        );

        $this->assertMaxRows(count($report['top_suppliers'] ?? []), 10, 'Top suppliers');
        $this->assertMaxRows(count($report['latest_invoices'] ?? []), 20, 'Latest purchase invoices');

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Purchase report integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Purchase report integrity check passed.');

        return self::SUCCESS;
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