<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\TreasuryTransaction;
use App\Services\Finance\FinancialReportService;
use Illuminate\Console\Command;

class FinancialSummaryIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-financial-summary
                            {--from= : Start date in Y-m-d format}
                            {--to= : End date in Y-m-d format}';

    protected $description = 'Checks financial summary report totals against treasury transactions and account balances.';

    private int $errors = 0;

    public function handle(): int
    {
        $fromDate = $this->option('from');
        $toDate = $this->option('to');

        $this->info('Starting financial summary integrity check...');
        $this->line('------------------------------------------');

        if ($fromDate || $toDate) {
            $this->line('Period: ' . ($fromDate ?: 'beginning') . ' → ' . ($toDate ?: 'today'));
        } else {
            $this->line('Period: all transactions');
        }

        $report = app(FinancialReportService::class)->summary($fromDate, $toDate);

        $this->checkMainTotals($report, $fromDate, $toDate);
        $this->checkCashAndBankTotals($report, $fromDate, $toDate);
        $this->checkAccountBalances($report);
        $this->checkTransactionTypeSummary($report, $fromDate, $toDate);
        $this->checkLatestTransactions($report);

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Financial summary integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Financial summary integrity check passed.');

        return self::SUCCESS;
    }

    private function checkMainTotals(array $report, ?string $fromDate, ?string $toDate): void
    {
        $this->info('Checking main inflow/outflow totals...');

        $expectedInflow = $this->sumTransactions(
            direction: TreasuryTransaction::DIRECTION_IN,
            fromDate: $fromDate,
            toDate: $toDate
        );

        $expectedOutflow = $this->sumTransactions(
            direction: TreasuryTransaction::DIRECTION_OUT,
            fromDate: $fromDate,
            toDate: $toDate
        );

        $this->assertClose(
            actual: (float) ($report['total_inflow'] ?? 0),
            expected: $expectedInflow,
            label: 'Total inflow'
        );

        $this->assertClose(
            actual: (float) ($report['total_outflow'] ?? 0),
            expected: $expectedOutflow,
            label: 'Total outflow'
        );

        $this->assertClose(
            actual: ((float) ($report['total_inflow'] ?? 0)) - ((float) ($report['total_outflow'] ?? 0)),
            expected: $expectedInflow - $expectedOutflow,
            label: 'Net movement'
        );
    }

    private function checkCashAndBankTotals(array $report, ?string $fromDate, ?string $toDate): void
    {
        $this->info('Checking cash and bank totals...');

        $cashInflow = $this->sumTransactions(
            direction: TreasuryTransaction::DIRECTION_IN,
            fromDate: $fromDate,
            toDate: $toDate,
            paymentChannel: TreasuryTransaction::CHANNEL_CASH
        );

        $cashOutflow = $this->sumTransactions(
            direction: TreasuryTransaction::DIRECTION_OUT,
            fromDate: $fromDate,
            toDate: $toDate,
            paymentChannel: TreasuryTransaction::CHANNEL_CASH
        );

        $bankInflow = $this->sumTransactions(
            direction: TreasuryTransaction::DIRECTION_IN,
            fromDate: $fromDate,
            toDate: $toDate,
            paymentChannel: TreasuryTransaction::CHANNEL_BANK
        );

        $bankOutflow = $this->sumTransactions(
            direction: TreasuryTransaction::DIRECTION_OUT,
            fromDate: $fromDate,
            toDate: $toDate,
            paymentChannel: TreasuryTransaction::CHANNEL_BANK
        );

        $this->assertClose((float) ($report['cash_inflow'] ?? 0), $cashInflow, 'Cash inflow');
        $this->assertClose((float) ($report['cash_outflow'] ?? 0), $cashOutflow, 'Cash outflow');
        $this->assertClose((float) ($report['bank_inflow'] ?? 0), $bankInflow, 'Bank inflow');
        $this->assertClose((float) ($report['bank_outflow'] ?? 0), $bankOutflow, 'Bank outflow');
    }

    private function checkAccountBalances(array $report): void
    {
        $this->info('Checking cashbox and bank current balances...');

        $expectedCashboxTotal = (float) Cashbox::query()->sum('current_balance');
        $expectedBankTotal = (float) BankAccount::query()->sum('current_balance');

        $this->assertClose(
            actual: (float) ($report['cashbox_total_balance'] ?? 0),
            expected: $expectedCashboxTotal,
            label: 'Cashbox total current balance'
        );

        $this->assertClose(
            actual: (float) ($report['bank_total_balance'] ?? 0),
            expected: $expectedBankTotal,
            label: 'Bank total current balance'
        );

        $cashboxRowsTotal = collect($report['cashboxes'] ?? [])->sum(fn (array $row): float => (float) ($row['current_balance'] ?? 0));
        $bankRowsTotal = collect($report['bank_accounts'] ?? [])->sum(fn (array $row): float => (float) ($row['current_balance'] ?? 0));

        $this->assertClose(
            actual: (float) $cashboxRowsTotal,
            expected: $expectedCashboxTotal,
            label: 'Cashbox rows current balance total'
        );

        $this->assertClose(
            actual: (float) $bankRowsTotal,
            expected: $expectedBankTotal,
            label: 'Bank rows current balance total'
        );
    }

    private function checkTransactionTypeSummary(array $report, ?string $fromDate, ?string $toDate): void
    {
        $this->info('Checking transaction type summary...');

        $expectedRows = TreasuryTransaction::query()
            ->when($fromDate, fn ($query) => $query->whereDate('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('transaction_date', '<=', $toDate))
            ->selectRaw('transaction_type, direction, COUNT(*) as transactions_count, SUM(amount) as total_amount')
            ->groupBy('transaction_type', 'direction')
            ->get()
            ->mapWithKeys(function (TreasuryTransaction $row): array {
                $key = $row->transaction_type . '|' . $row->direction;

                return [
                    $key => [
                        'transactions_count' => (int) $row->transactions_count,
                        'total_amount' => (float) $row->total_amount,
                    ],
                ];
            });

        $actualRows = collect($report['transaction_type_summary'] ?? [])
            ->mapWithKeys(function (array $row): array {
                $key = ($row['transaction_type'] ?? '') . '|' . ($row['direction'] ?? '');

                return [
                    $key => [
                        'transactions_count' => (int) ($row['transactions_count'] ?? 0),
                        'total_amount' => (float) ($row['total_amount'] ?? 0),
                    ],
                ];
            });

        foreach ($expectedRows as $key => $expected) {
            if (! $actualRows->has($key)) {
                $this->errors++;
                $this->error("✗ Transaction summary row missing: {$key}");

                continue;
            }

            $actual = $actualRows->get($key);

            if ((int) $actual['transactions_count'] !== (int) $expected['transactions_count']) {
                $this->errors++;
                $this->error("✗ Transaction count {$key}: expected {$expected['transactions_count']}, actual {$actual['transactions_count']}");
            } else {
                $this->line("✓ Transaction count {$key}");
            }

            $this->assertClose(
                actual: (float) $actual['total_amount'],
                expected: (float) $expected['total_amount'],
                label: "Transaction amount {$key}"
            );
        }

        foreach ($actualRows as $key => $actual) {
            if (! $expectedRows->has($key)) {
                $this->errors++;
                $this->error("✗ Unexpected transaction summary row: {$key}");
            }
        }
    }

    private function checkLatestTransactions(array $report): void
    {
        $this->info('Checking latest transactions block...');

        $rows = $report['latest_transactions'] ?? [];

        if (count($rows) > 20) {
            $this->errors++;
            $this->error('✗ Latest transactions block contains more than 20 rows.');

            return;
        }

        $this->line('✓ Latest transactions row limit');
    }

    private function sumTransactions(
        string $direction,
        ?string $fromDate,
        ?string $toDate,
        ?string $paymentChannel = null
    ): float {
        return (float) TreasuryTransaction::query()
            ->where('direction', $direction)
            ->when($paymentChannel, fn ($query) => $query->where('payment_channel', $paymentChannel))
            ->when($fromDate, fn ($query) => $query->whereDate('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('transaction_date', '<=', $toDate))
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
}