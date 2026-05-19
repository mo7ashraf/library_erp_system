<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ErpCheckAllCommand extends Command
{
    protected $signature = 'erp:check-all';

    protected $description = 'Runs all ERP development checks.';

    public function handle(): int
    {
        $this->info('Running all ERP checks...');
        $this->line('==========================================');

        $inventoryResult = $this->call('inventory:check-flow');

        $this->line('==========================================');

        $financeResult = $this->call('finance:check-flow');

        $this->line('==========================================');

        $documentsResult = $this->call('erp:check-documents');

        $this->line('==========================================');

        $ledgersResult = $this->call('erp:check-ledgers');

        $this->line('==========================================');

        $financialSummaryResult = $this->call('erp:check-financial-summary');

        $this->line('==========================================');

        if (
            $inventoryResult !== self::SUCCESS
            || $financeResult !== self::SUCCESS
            || $documentsResult !== self::SUCCESS
            || $ledgersResult !== self::SUCCESS
            || $financialSummaryResult !== self::SUCCESS
        ) {
            $this->error('One or more ERP checks failed.');

            return self::FAILURE;
        }

        $this->info('✓ All ERP checks passed.');

        return self::SUCCESS;
    }
}