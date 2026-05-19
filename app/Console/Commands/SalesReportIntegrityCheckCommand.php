<?php

namespace App\Console\Commands;

use App\Models\SalesInvoice;
use App\Services\Reports\SalesReportService;
use Illuminate\Console\Command;

class SalesReportIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-sales-report
                            {--from= : Start date in Y-m-d format}
                            {--to= : End date in Y-m-d format}';

    protected $description = 'Checks sales summary report totals against posted sales invoices.';

    private int $errors = 0;

    public function handle(): int
    {
        $fromDate = $this->option('from') ?: now()->startOfMonth()->toDateString();
        $toDate = $this->option('to') ?: now()->toDateString();

        $this->info('Starting sales report integrity check...');
        $this->line('------------------------------------------');
        $this->line("Period: {$fromDate} → {$toDate}");

        $report = app(SalesReportService::class)->summary($fromDate, $toDate);

        $baseQuery = SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate);

        $this->assertEqualsInt(
            actual: (int) ($report['totals']['invoices_count'] ?? 0),
            expected: (clone $baseQuery)->count(),
            label: 'Sales invoices count'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['subtotal'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('subtotal'),
            label: 'Sales subtotal'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['discount_amount'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('discount_amount'),
            label: 'Sales discount'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['service_amount'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('service_amount'),
            label: 'Sales service'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['commission_amount'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('commission_amount'),
            label: 'Sales commission'
        );

        $this->assertClose(
            actual: (float) ($report['totals']['grand_total'] ?? 0),
            expected: (float) (clone $baseQuery)->sum('grand_total'),
            label: 'Sales grand total'
        );

        $this->assertMaxRows(count($report['top_customers'] ?? []), 10, 'Top customers');
        $this->assertMaxRows(count($report['latest_invoices'] ?? []), 20, 'Latest sales invoices');

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Sales report integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Sales report integrity check passed.');

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