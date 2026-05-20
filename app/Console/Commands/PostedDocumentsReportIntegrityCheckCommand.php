<?php

namespace App\Console\Commands;

use App\Services\Reports\PostedDocumentsReportService;
use Illuminate\Console\Command;

class PostedDocumentsReportIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-posted-documents-report
                            {--from= : Start date in Y-m-d format}
                            {--to= : End date in Y-m-d format}
                            {--status=all : all, posted, or draft}';

    protected $description = 'Checks posted documents audit report totals and grouping consistency.';

    private int $errors = 0;

    public function handle(): int
    {
        $fromDate = $this->option('from') ?: now()->startOfMonth()->toDateString();
        $toDate = $this->option('to') ?: now()->toDateString();
        $status = $this->option('status') ?: 'all';

        $this->info('Starting posted documents report integrity check...');
        $this->line('------------------------------------------');
        $this->line("Period: {$fromDate} → {$toDate}");
        $this->line("Status: {$status}");

        $report = app(PostedDocumentsReportService::class)->summary($fromDate, $toDate, $status);

        $rows = collect($report['rows'] ?? []);
        $totals = $report['totals'] ?? [];

        $this->assertEqualsInt(
            actual: (int) ($totals['documents_count'] ?? 0),
            expected: $rows->count(),
            label: 'Documents count'
        );

        $this->assertEqualsInt(
            actual: (int) ($totals['posted_count'] ?? 0),
            expected: $rows->where('status', 'posted')->count(),
            label: 'Posted documents count'
        );

        $this->assertEqualsInt(
            actual: (int) ($totals['draft_count'] ?? 0),
            expected: $rows->where('status', 'draft')->count(),
            label: 'Draft documents count'
        );

        $this->assertClose(
            actual: (float) ($totals['total_amount'] ?? 0),
            expected: $rows->sum(fn (array $row): float => (float) ($row['amount'] ?? 0)),
            label: 'Total amount'
        );

        $this->assertClose(
            actual: (float) ($totals['total_quantity'] ?? 0),
            expected: $rows->sum(fn (array $row): float => (float) ($row['quantity'] ?? 0)),
            label: 'Total quantity'
        );

        $this->checkDocumentTypeGroups($report);
        $this->checkStatusGroups($report);

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Posted documents report integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Posted documents report integrity check passed.');

        return self::SUCCESS;
    }

    private function checkDocumentTypeGroups(array $report): void
    {
        $this->info('Checking document type groups...');

        $rows = collect($report['rows'] ?? []);
        $groups = collect($report['by_document_type'] ?? []);

        foreach ($groups as $group) {
            $typeKey = $group['document_type_key'];

            $matchingRows = $rows->where('document_type_key', $typeKey);

            $this->assertEqualsInt(
                actual: (int) $group['documents_count'],
                expected: $matchingRows->count(),
                label: "Document type {$typeKey} count"
            );

            $this->assertClose(
                actual: (float) $group['total_amount'],
                expected: $matchingRows->sum(fn (array $row): float => (float) ($row['amount'] ?? 0)),
                label: "Document type {$typeKey} total"
            );
        }
    }

    private function checkStatusGroups(array $report): void
    {
        $this->info('Checking status groups...');

        $rows = collect($report['rows'] ?? []);
        $groups = collect($report['by_status'] ?? []);

        foreach ($groups as $group) {
            $status = $group['status'];

            $matchingRows = $rows->where('status', $status);

            $this->assertEqualsInt(
                actual: (int) $group['documents_count'],
                expected: $matchingRows->count(),
                label: "Status {$status} count"
            );

            $this->assertClose(
                actual: (float) $group['total_amount'],
                expected: $matchingRows->sum(fn (array $row): float => (float) ($row['amount'] ?? 0)),
                label: "Status {$status} total"
            );
        }
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
}