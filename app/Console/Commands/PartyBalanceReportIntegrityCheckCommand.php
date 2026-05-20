<?php

namespace App\Console\Commands;

use App\Services\Reports\PartyBalanceReportService;
use Illuminate\Console\Command;

class PartyBalanceReportIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-party-balance-reports
                            {--from= : Start date in Y-m-d format}
                            {--to= : End date in Y-m-d format}';

    protected $description = 'Checks customer and supplier balance reports totals and row consistency.';

    private int $errors = 0;

    public function handle(): int
    {
        $fromDate = $this->option('from') ?: null;
        $toDate = $this->option('to') ?: now()->toDateString();

        $this->info('Starting party balance reports integrity check...');
        $this->line('------------------------------------------');
        $this->line('Period: ' . ($fromDate ?: 'beginning') . " → {$toDate}");

        $service = app(PartyBalanceReportService::class);

        $this->checkReport(
            report: $service->customerBalances($fromDate, $toDate),
            label: 'Customer balance report'
        );

        $this->line('------------------------------------------');

        $this->checkReport(
            report: $service->supplierBalances($fromDate, $toDate),
            label: 'Supplier balance report'
        );

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Party balance reports integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Party balance reports integrity check passed.');

        return self::SUCCESS;
    }

    private function checkReport(array $report, string $label): void
    {
        $this->info("Checking {$label}...");

        $rows = collect($report['rows'] ?? []);
        $totals = $report['totals'] ?? [];

        $debitRows = $rows->filter(fn (array $row): bool => (float) ($row['closing_balance'] ?? 0) > 0);
        $creditRows = $rows->filter(fn (array $row): bool => (float) ($row['closing_balance'] ?? 0) < 0);
        $zeroRows = $rows->filter(fn (array $row): bool => abs((float) ($row['closing_balance'] ?? 0)) < 0.01);

        $this->assertEqualsInt(
            actual: (int) ($totals['parties_count'] ?? 0),
            expected: $rows->count(),
            label: "{$label} parties count"
        );

        $this->assertEqualsInt(
            actual: (int) ($totals['debit_parties_count'] ?? 0),
            expected: $debitRows->count(),
            label: "{$label} debit parties count"
        );

        $this->assertEqualsInt(
            actual: (int) ($totals['credit_parties_count'] ?? 0),
            expected: $creditRows->count(),
            label: "{$label} credit parties count"
        );

        $this->assertEqualsInt(
            actual: (int) ($totals['zero_parties_count'] ?? 0),
            expected: $zeroRows->count(),
            label: "{$label} zero parties count"
        );

        $this->assertClose(
            actual: (float) ($totals['period_debit_total'] ?? 0),
            expected: $rows->sum(fn (array $row): float => (float) ($row['period_debit'] ?? 0)),
            label: "{$label} period debit total"
        );

        $this->assertClose(
            actual: (float) ($totals['period_credit_total'] ?? 0),
            expected: $rows->sum(fn (array $row): float => (float) ($row['period_credit'] ?? 0)),
            label: "{$label} period credit total"
        );

        $this->assertClose(
            actual: (float) ($totals['closing_debit_total'] ?? 0),
            expected: $debitRows->sum(fn (array $row): float => (float) ($row['closing_balance'] ?? 0)),
            label: "{$label} closing debit total"
        );

        $this->assertClose(
            actual: (float) ($totals['closing_credit_total'] ?? 0),
            expected: abs($creditRows->sum(fn (array $row): float => (float) ($row['closing_balance'] ?? 0))),
            label: "{$label} closing credit total"
        );

        $this->assertClose(
            actual: (float) ($totals['net_balance'] ?? 0),
            expected: $rows->sum(fn (array $row): float => (float) ($row['closing_balance'] ?? 0)),
            label: "{$label} net balance"
        );

        foreach ($rows as $index => $row) {
            $expectedClosing = (float) ($row['opening_balance'] ?? 0)
                + (float) ($row['period_debit'] ?? 0)
                - (float) ($row['period_credit'] ?? 0);

            $this->assertClose(
                actual: (float) ($row['closing_balance'] ?? 0),
                expected: $expectedClosing,
                label: "{$label} row #" . ($index + 1) . ' closing balance'
            );
        }

        $this->assertMaxRows(count($report['top_debit'] ?? []), 10, "{$label} top debit");
        $this->assertMaxRows(count($report['top_credit'] ?? []), 10, "{$label} top credit");
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