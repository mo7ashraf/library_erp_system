<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ReleaseReadinessCheckCommand extends Command
{
    protected $signature = 'erp:check-release-readiness
                            {--run-checks : Also run erp:check-all after static readiness checks}';

    protected $description = 'Checks whether the ERP system is ready for a stable release.';

    private int $errors = 0;

    public function handle(): int
    {
        $this->info('Starting ERP release readiness check...');
        $this->line('------------------------------------------');

        $this->checkRoutes();
        $this->checkViews();
        $this->checkPrintViews();
        $this->checkServices();
        $this->checkCommands();

        if ($this->option('run-checks')) {
            $this->line('------------------------------------------');
            $this->info('Running full ERP integrity checks...');

            $result = $this->call('erp:check-all');

            if ($result !== self::SUCCESS) {
                $this->errors++;
                $this->error('✗ erp:check-all failed.');
            } else {
                $this->line('✓ erp:check-all passed.');
            }
        }

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("ERP release readiness check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ ERP release readiness check passed.');

        return self::SUCCESS;
    }

    private function checkRoutes(): void
    {
        $this->info('Checking print routes...');

        $requiredRoutes = [
            'admin.prints.sales-invoices.receipt',
            'admin.prints.stock-transfers.receipt',
            'admin.prints.sales-returns.receipt',
            'admin.prints.purchase-returns.receipt',
            'admin.prints.stock-count-documents.receipt',
            'admin.prints.damaged-stock-documents.receipt',
            'admin.prints.receipt-vouchers.receipt',
            'admin.prints.payment-vouchers.receipt',
            'admin.prints.customer-ledger',
            'admin.prints.supplier-ledger',
            'admin.prints.financial-summary-report',
            'admin.prints.sales-summary-report',
            'admin.prints.purchase-summary-report',
            'admin.prints.inventory-summary-report',
            'admin.prints.customer-balance-report',
            'admin.prints.supplier-balance-report',
            'admin.prints.posted-documents-report',
        ];

        foreach ($requiredRoutes as $routeName) {
            if (! Route::has($routeName)) {
                $this->errors++;
                $this->error("✗ Missing route: {$routeName}");

                continue;
            }

            $this->line("✓ Route exists: {$routeName}");
        }
    }

    private function checkViews(): void
    {
        $this->info('Checking Filament report views...');

        $requiredViews = [
            'resources/views/filament/pages/erp-dashboard.blade.php',
            'resources/views/filament/pages/financial-summary-report.blade.php',
            'resources/views/filament/pages/sales-summary-report.blade.php',
            'resources/views/filament/pages/purchase-summary-report.blade.php',
            'resources/views/filament/pages/inventory-summary-report.blade.php',
            'resources/views/filament/pages/customer-balance-report.blade.php',
            'resources/views/filament/pages/supplier-balance-report.blade.php',
            'resources/views/filament/pages/posted-documents-report.blade.php',
            'resources/views/filament/pages/customer-ledger.blade.php',
            'resources/views/filament/pages/supplier-ledger.blade.php',
            'resources/views/filament/pages/partials/party-balance-report.blade.php',
        ];

        $this->checkFiles($requiredViews, 'View');
    }

    private function checkPrintViews(): void
    {
        $this->info('Checking print views...');

        $requiredPrintViews = [
            'resources/views/prints/sales-invoice-receipt.blade.php',
            'resources/views/prints/stock-transfer-receipt.blade.php',
            'resources/views/prints/sales-return-receipt.blade.php',
            'resources/views/prints/purchase-return-receipt.blade.php',
            'resources/views/prints/stock-count-receipt.blade.php',
            'resources/views/prints/damaged-stock-receipt.blade.php',
            'resources/views/prints/receipt-voucher-receipt.blade.php',
            'resources/views/prints/payment-voucher-receipt.blade.php',
            'resources/views/prints/customer-ledger-print.blade.php',
            'resources/views/prints/supplier-ledger-print.blade.php',
            'resources/views/prints/financial-summary-report-print.blade.php',
            'resources/views/prints/sales-summary-report-print.blade.php',
            'resources/views/prints/purchase-summary-report-print.blade.php',
            'resources/views/prints/inventory-summary-report-print.blade.php',
            'resources/views/prints/party-balance-report-print.blade.php',
            'resources/views/prints/posted-documents-report-print.blade.php',
        ];

        $this->checkFiles($requiredPrintViews, 'Print view');
    }

    private function checkServices(): void
    {
        $this->info('Checking services...');

        $requiredServices = [
            #'app/Services/Inventory/InventoryService.php',
            'app/Services/Finance/TreasuryService.php',
            'app/Services/Finance/PartyLedgerService.php',
            'app/Services/Finance/FinancialReportService.php',
            'app/Services/Dashboard/ErpDashboardService.php',
            'app/Services/Reports/SalesReportService.php',
            'app/Services/Reports/PurchaseReportService.php',
            'app/Services/Reports/InventoryReportService.php',
            'app/Services/Reports/PartyBalanceReportService.php',
            'app/Services/Reports/PostedDocumentsReportService.php',
        ];

        $this->checkFiles($requiredServices, 'Service');
    }

    private function checkCommands(): void
    {
        $this->info('Checking artisan commands...');

        $registeredCommands = array_keys(Artisan::all());

        $requiredCommands = [
            'inventory:check-flow',
            'finance:check-flow',
            'erp:check-documents',
            'erp:check-ledgers',
            'erp:check-financial-summary',
            'erp:check-dashboard',
            'erp:check-sales-report',
            'erp:check-purchase-report',
            'erp:check-inventory-report',
            'erp:check-party-balance-reports',
            'erp:check-posted-documents-report',
            'erp:check-all',
        ];

        foreach ($requiredCommands as $commandName) {
            if (! in_array($commandName, $registeredCommands, true)) {
                $this->errors++;
                $this->error("✗ Missing command: {$commandName}");

                continue;
            }

            $this->line("✓ Command exists: {$commandName}");
        }
    }

    private function checkFiles(array $paths, string $label): void
    {
        foreach ($paths as $path) {
            $fullPath = base_path($path);

            if (! File::exists($fullPath)) {
                $this->errors++;
                $this->error("✗ Missing {$label}: {$path}");

                continue;
            }

            $this->line("✓ {$label} exists: {$path}");
        }
    }
}