<?php

namespace App\Services\Reports;

use App\Models\Customer;
use App\Models\Supplier;
use App\Services\Finance\PartyLedgerService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PartyBalanceReportService
{
    public function customerBalances(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $this->normalizeDate($fromDate);
        $toDate = $this->normalizeDate($toDate) ?: now()->toDateString();

        $rows = Customer::query()
            ->with('branch')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Customer $customer) use ($fromDate, $toDate): array {
                $ledger = app(PartyLedgerService::class)->customerLedger(
                    customerId: $customer->id,
                    fromDate: $fromDate,
                    toDate: $toDate
                );

                $closingBalance = (float) ($ledger['closing_balance'] ?? 0);

                return [
                    'id' => $customer->id,
                    'code' => $customer->code,
                    'name' => $customer->name,
                    'branch_name' => $customer->branch?->name ?? '-',
                    'phone' => $customer->mobile ?: $customer->phone ?: '-',
                    'opening_balance' => (float) ($ledger['opening_balance'] ?? 0),
                    'period_debit' => (float) ($ledger['total_debit'] ?? 0),
                    'period_credit' => (float) ($ledger['total_credit'] ?? 0),
                    'closing_balance' => $closingBalance,
                    'closing_balance_label' => $ledger['closing_balance_label'] ?? $this->formatSignedBalance($closingBalance),
                    'balance_side' => $this->balanceSide($closingBalance),
                    'rows_count' => count($ledger['rows'] ?? []),
                    'ledger_url' => url('/admin/customer-ledger?customer_id=' . $customer->id . '&from_date=' . ($fromDate ?? '') . '&to_date=' . ($toDate ?? '')),
                ];
            })
            ->values();

        return $this->buildResult(
            reportType: 'customers',
            title: 'تقرير أرصدة العملاء',
            fromDate: $fromDate,
            toDate: $toDate,
            rows: $rows
        );
    }

    public function supplierBalances(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $this->normalizeDate($fromDate);
        $toDate = $this->normalizeDate($toDate) ?: now()->toDateString();

        $rows = Supplier::query()
            ->with('branch')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Supplier $supplier) use ($fromDate, $toDate): array {
                $ledger = app(PartyLedgerService::class)->supplierLedger(
                    supplierId: $supplier->id,
                    fromDate: $fromDate,
                    toDate: $toDate
                );

                $closingBalance = (float) ($ledger['closing_balance'] ?? 0);

                return [
                    'id' => $supplier->id,
                    'code' => $supplier->code,
                    'name' => $supplier->name,
                    'branch_name' => $supplier->branch?->name ?? '-',
                    'phone' => $supplier->mobile ?: $supplier->phone ?: '-',
                    'opening_balance' => (float) ($ledger['opening_balance'] ?? 0),
                    'period_debit' => (float) ($ledger['total_debit'] ?? 0),
                    'period_credit' => (float) ($ledger['total_credit'] ?? 0),
                    'closing_balance' => $closingBalance,
                    'closing_balance_label' => $ledger['closing_balance_label'] ?? $this->formatSignedBalance($closingBalance),
                    'balance_side' => $this->balanceSide($closingBalance),
                    'rows_count' => count($ledger['rows'] ?? []),
                    'ledger_url' => url('/admin/supplier-ledger?supplier_id=' . $supplier->id . '&from_date=' . ($fromDate ?? '') . '&to_date=' . ($toDate ?? '')),
                ];
            })
            ->values();

        return $this->buildResult(
            reportType: 'suppliers',
            title: 'تقرير أرصدة الموردين',
            fromDate: $fromDate,
            toDate: $toDate,
            rows: $rows
        );
    }

    private function buildResult(string $reportType, string $title, ?string $fromDate, ?string $toDate, Collection $rows): array
    {
        $debitRows = $rows->filter(fn (array $row): bool => (float) $row['closing_balance'] > 0);
        $creditRows = $rows->filter(fn (array $row): bool => (float) $row['closing_balance'] < 0);
        $zeroRows = $rows->filter(fn (array $row): bool => abs((float) $row['closing_balance']) < 0.01);

        return [
            'report_type' => $reportType,
            'title' => $title,
            'from_date' => $fromDate,
            'to_date' => $toDate,

            'totals' => [
                'parties_count' => $rows->count(),
                'debit_parties_count' => $debitRows->count(),
                'credit_parties_count' => $creditRows->count(),
                'zero_parties_count' => $zeroRows->count(),

                'opening_debit_total' => $rows->sum(fn (array $row): float => max((float) $row['opening_balance'], 0)),
                'opening_credit_total' => abs($rows->sum(fn (array $row): float => min((float) $row['opening_balance'], 0))),

                'period_debit_total' => $rows->sum(fn (array $row): float => (float) $row['period_debit']),
                'period_credit_total' => $rows->sum(fn (array $row): float => (float) $row['period_credit']),

                'closing_debit_total' => $debitRows->sum(fn (array $row): float => (float) $row['closing_balance']),
                'closing_credit_total' => abs($creditRows->sum(fn (array $row): float => (float) $row['closing_balance'])),
                'net_balance' => $rows->sum(fn (array $row): float => (float) $row['closing_balance']),
            ],

            'rows' => $rows
                ->sortByDesc(fn (array $row): float => abs((float) $row['closing_balance']))
                ->values()
                ->toArray(),

            'top_debit' => $debitRows
                ->sortByDesc(fn (array $row): float => (float) $row['closing_balance'])
                ->take(10)
                ->values()
                ->toArray(),

            'top_credit' => $creditRows
                ->sortBy(fn (array $row): float => (float) $row['closing_balance'])
                ->take(10)
                ->values()
                ->toArray(),
        ];
    }

    private function balanceSide(float $balance): string
    {
        if ($balance > 0) {
            return 'debit';
        }

        if ($balance < 0) {
            return 'credit';
        }

        return 'zero';
    }

    private function formatSignedBalance(float $balance): string
    {
        if ($balance > 0) {
            return number_format($balance, 2) . ' مدين';
        }

        if ($balance < 0) {
            return number_format(abs($balance), 2) . ' دائن';
        }

        return number_format(0, 2);
    }

    private function normalizeDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}