<?php

namespace App\Services\Finance;

use App\Models\Customer;
use App\Models\PaymentVoucher;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\ReceiptVoucher;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PartyLedgerService
{
    public function customerLedger(int $customerId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $customer = Customer::findOrFail($customerId);

        $fromDate = $this->normalizeDate($fromDate);
        $toDate = $this->normalizeDate($toDate);

        $openingBalance = $this->openingSignedBalance(
            (float) $customer->opening_balance,
            $customer->balance_type
        );

        $allRows = $this->collectCustomerRows($customerId, $toDate);

        $openingRows = $fromDate
            ? $allRows->filter(fn (array $row): bool => $row['date'] < $fromDate)
            : collect();

        foreach ($openingRows as $row) {
            $openingBalance += (float) $row['debit'] - (float) $row['credit'];
        }

        $periodRows = $allRows
            ->filter(function (array $row) use ($fromDate, $toDate): bool {
                if ($fromDate && $row['date'] < $fromDate) {
                    return false;
                }

                if ($toDate && $row['date'] > $toDate) {
                    return false;
                }

                return true;
            })
            ->values();

        return $this->buildLedgerResult(
            partyName: $customer->name,
            partyCode: $customer->code,
            fromDate: $fromDate,
            toDate: $toDate,
            openingBalance: $openingBalance,
            rows: $periodRows
        );
    }

    public function supplierLedger(int $supplierId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $supplier = Supplier::findOrFail($supplierId);

        $fromDate = $this->normalizeDate($fromDate);
        $toDate = $this->normalizeDate($toDate);

        $openingBalance = $this->openingSignedBalance(
            (float) $supplier->opening_balance,
            $supplier->balance_type
        );

        $allRows = $this->collectSupplierRows($supplierId, $toDate);

        $openingRows = $fromDate
            ? $allRows->filter(fn (array $row): bool => $row['date'] < $fromDate)
            : collect();

        foreach ($openingRows as $row) {
            $openingBalance += (float) $row['debit'] - (float) $row['credit'];
        }

        $periodRows = $allRows
            ->filter(function (array $row) use ($fromDate, $toDate): bool {
                if ($fromDate && $row['date'] < $fromDate) {
                    return false;
                }

                if ($toDate && $row['date'] > $toDate) {
                    return false;
                }

                return true;
            })
            ->values();

        return $this->buildLedgerResult(
            partyName: $supplier->name,
            partyCode: $supplier->code,
            fromDate: $fromDate,
            toDate: $toDate,
            openingBalance: $openingBalance,
            rows: $periodRows
        );
    }

    private function collectCustomerRows(int $customerId, ?string $toDate = null): Collection
    {
        $rows = collect();

        SalesInvoice::query()
            ->where('customer_id', $customerId)
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('invoice_date', '<=', $toDate))
            ->get()
            ->each(function (SalesInvoice $invoice) use ($rows): void {
                $rows->push([
                    'date' => $invoice->invoice_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 10,
                    'document_type' => 'فاتورة مبيعات',
                    'reference_number' => $invoice->invoice_number,
                    'description' => $invoice->notes ?: 'فاتورة مبيعات',
                    'debit' => (float) $invoice->grand_total,
                    'credit' => 0,
                ]);
            });

        SalesReturn::query()
            ->where('customer_id', $customerId)
            ->where('status', SalesReturn::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('return_date', '<=', $toDate))
            ->get()
            ->each(function (SalesReturn $return) use ($rows): void {
                $rows->push([
                    'date' => $return->return_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 20,
                    'document_type' => 'مرتجع مبيعات',
                    'reference_number' => $return->return_number,
                    'description' => $return->notes ?: 'مرتجع مبيعات',
                    'debit' => 0,
                    'credit' => (float) $return->grand_total,
                ]);
            });

        ReceiptVoucher::query()
            ->where('party_type', ReceiptVoucher::PARTY_CUSTOMER)
            ->where('customer_id', $customerId)
            ->where('status', ReceiptVoucher::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('voucher_date', '<=', $toDate))
            ->get()
            ->each(function (ReceiptVoucher $voucher) use ($rows): void {
                $rows->push([
                    'date' => $voucher->voucher_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 30,
                    'document_type' => 'سند قبض',
                    'reference_number' => $voucher->voucher_number,
                    'description' => $voucher->description ?: 'سند قبض من العميل',
                    'debit' => 0,
                    'credit' => (float) $voucher->amount,
                ]);
            });

        PaymentVoucher::query()
            ->where('party_type', PaymentVoucher::PARTY_CUSTOMER)
            ->where('customer_id', $customerId)
            ->where('status', PaymentVoucher::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('voucher_date', '<=', $toDate))
            ->get()
            ->each(function (PaymentVoucher $voucher) use ($rows): void {
                $rows->push([
                    'date' => $voucher->voucher_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 40,
                    'document_type' => 'سند صرف',
                    'reference_number' => $voucher->voucher_number,
                    'description' => $voucher->description ?: 'سند صرف للعميل',
                    'debit' => (float) $voucher->amount,
                    'credit' => 0,
                ]);
            });

        return $this->sortRows($rows);
    }

    private function collectSupplierRows(int $supplierId, ?string $toDate = null): Collection
    {
        $rows = collect();

        PurchaseInvoice::query()
            ->where('supplier_id', $supplierId)
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('invoice_date', '<=', $toDate))
            ->get()
            ->each(function (PurchaseInvoice $invoice) use ($rows): void {
                $rows->push([
                    'date' => $invoice->invoice_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 10,
                    'document_type' => 'فاتورة مشتريات',
                    'reference_number' => $invoice->invoice_number,
                    'description' => $invoice->notes ?: 'فاتورة مشتريات',
                    'debit' => 0,
                    'credit' => (float) $invoice->grand_total,
                ]);
            });

        PurchaseReturn::query()
            ->where('supplier_id', $supplierId)
            ->where('status', PurchaseReturn::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('return_date', '<=', $toDate))
            ->get()
            ->each(function (PurchaseReturn $return) use ($rows): void {
                $rows->push([
                    'date' => $return->return_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 20,
                    'document_type' => 'مرتجع مشتريات',
                    'reference_number' => $return->return_number,
                    'description' => $return->notes ?: 'مرتجع مشتريات',
                    'debit' => (float) $return->grand_total,
                    'credit' => 0,
                ]);
            });

        PaymentVoucher::query()
            ->where('party_type', PaymentVoucher::PARTY_SUPPLIER)
            ->where('supplier_id', $supplierId)
            ->where('status', PaymentVoucher::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('voucher_date', '<=', $toDate))
            ->get()
            ->each(function (PaymentVoucher $voucher) use ($rows): void {
                $rows->push([
                    'date' => $voucher->voucher_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 30,
                    'document_type' => 'سند صرف',
                    'reference_number' => $voucher->voucher_number,
                    'description' => $voucher->description ?: 'سند صرف للمورد',
                    'debit' => (float) $voucher->amount,
                    'credit' => 0,
                ]);
            });

        ReceiptVoucher::query()
            ->where('party_type', ReceiptVoucher::PARTY_SUPPLIER)
            ->where('supplier_id', $supplierId)
            ->where('status', ReceiptVoucher::STATUS_POSTED)
            ->when($toDate, fn ($query) => $query->whereDate('voucher_date', '<=', $toDate))
            ->get()
            ->each(function (ReceiptVoucher $voucher) use ($rows): void {
                $rows->push([
                    'date' => $voucher->voucher_date?->format('Y-m-d') ?? now()->toDateString(),
                    'sequence' => 40,
                    'document_type' => 'سند قبض',
                    'reference_number' => $voucher->voucher_number,
                    'description' => $voucher->description ?: 'سند قبض من المورد',
                    'debit' => (float) $voucher->amount,
                    'credit' => 0,
                ]);
            });

        return $this->sortRows($rows);
    }

    private function buildLedgerResult(
        string $partyName,
        ?string $partyCode,
        ?string $fromDate,
        ?string $toDate,
        float $openingBalance,
        Collection $rows
    ): array {
        $runningBalance = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;

        $ledgerRows = $rows
            ->map(function (array $row) use (&$runningBalance, &$totalDebit, &$totalCredit): array {
                $debit = (float) $row['debit'];
                $credit = (float) $row['credit'];

                $totalDebit += $debit;
                $totalCredit += $credit;

                $runningBalance += $debit - $credit;

                $row['balance'] = $runningBalance;
                $row['balance_label'] = $this->formatSignedBalance($runningBalance);

                return $row;
            })
            ->values()
            ->toArray();

        return [
            'party_name' => $partyName,
            'party_code' => $partyCode,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'opening_balance' => $openingBalance,
            'opening_balance_label' => $this->formatSignedBalance($openingBalance),
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'closing_balance' => $runningBalance,
            'closing_balance_label' => $this->formatSignedBalance($runningBalance),
            'rows' => $ledgerRows,
        ];
    }

    private function sortRows(Collection $rows): Collection
    {
        return $rows
            ->sortBy(fn (array $row): string => $row['date'] . '-' . str_pad((string) $row['sequence'], 5, '0', STR_PAD_LEFT) . '-' . $row['reference_number'])
            ->values();
    }

    private function openingSignedBalance(float $amount, ?string $balanceType): float
    {
        $normalized = Str::lower(trim((string) $balanceType));

        if (in_array($normalized, ['credit', 'cr', 'creditor', 'دائن'], true)) {
            return -1 * abs($amount);
        }

        return abs($amount);
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