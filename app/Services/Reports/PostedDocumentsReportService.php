<?php

namespace App\Services\Reports;

use App\Models\DamagedStockDocument;
use App\Models\PaymentVoucher;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\ReceiptVoucher;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\StockCountDocument;
use App\Models\StockTransfer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PostedDocumentsReportService
{
    public function summary(?string $fromDate = null, ?string $toDate = null, string $status = 'all'): array
    {
        $fromDate = $this->normalizeDate($fromDate) ?: now()->startOfMonth()->toDateString();
        $toDate = $this->normalizeDate($toDate) ?: now()->toDateString();

        $rows = collect()
            ->merge($this->salesInvoices($fromDate, $toDate, $status))
            ->merge($this->purchaseInvoices($fromDate, $toDate, $status))
            ->merge($this->salesReturns($fromDate, $toDate, $status))
            ->merge($this->purchaseReturns($fromDate, $toDate, $status))
            ->merge($this->stockTransfers($fromDate, $toDate, $status))
            ->merge($this->stockCounts($fromDate, $toDate, $status))
            ->merge($this->damagedStockDocuments($fromDate, $toDate, $status))
            ->merge($this->receiptVouchers($fromDate, $toDate, $status))
            ->merge($this->paymentVouchers($fromDate, $toDate, $status))
            ->sortByDesc(fn (array $row): string => $row['document_date'] . '-' . str_pad((string) $row['id'], 10, '0', STR_PAD_LEFT))
            ->values();

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'status' => $status,
            'totals' => $this->totals($rows),
            'by_document_type' => $this->byDocumentType($rows),
            'by_status' => $this->byStatus($rows),
            'rows' => $rows->toArray(),
        ];
    }

    private function salesInvoices(string $fromDate, string $toDate, string $status): Collection
    {
        return SalesInvoice::query()
            ->with(['customer', 'branch', 'user'])
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (SalesInvoice $row): array => $this->row(
                id: $row->id,
                typeKey: 'sales_invoice',
                typeLabel: 'فاتورة مبيعات',
                number: $row->invoice_number,
                date: $row->invoice_date?->format('Y-m-d') ?? '-',
                party: $row->customer?->name ?? '-',
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->grand_total,
                quantity: null,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function purchaseInvoices(string $fromDate, string $toDate, string $status): Collection
    {
        return PurchaseInvoice::query()
            ->with(['supplier', 'branch', 'user'])
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (PurchaseInvoice $row): array => $this->row(
                id: $row->id,
                typeKey: 'purchase_invoice',
                typeLabel: 'فاتورة مشتريات',
                number: $row->invoice_number,
                date: $row->invoice_date?->format('Y-m-d') ?? '-',
                party: $row->supplier?->name ?? '-',
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->grand_total,
                quantity: null,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function salesReturns(string $fromDate, string $toDate, string $status): Collection
    {
        return SalesReturn::query()
            ->with(['customer', 'branch', 'user'])
            ->whereDate('return_date', '>=', $fromDate)
            ->whereDate('return_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (SalesReturn $row): array => $this->row(
                id: $row->id,
                typeKey: 'sales_return',
                typeLabel: 'مرتجع مبيعات',
                number: $row->return_number,
                date: $row->return_date?->format('Y-m-d') ?? '-',
                party: $row->customer?->name ?? '-',
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->grand_total,
                quantity: null,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function purchaseReturns(string $fromDate, string $toDate, string $status): Collection
    {
        return PurchaseReturn::query()
            ->with(['supplier', 'branch', 'user'])
            ->whereDate('return_date', '>=', $fromDate)
            ->whereDate('return_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (PurchaseReturn $row): array => $this->row(
                id: $row->id,
                typeKey: 'purchase_return',
                typeLabel: 'مرتجع مشتريات',
                number: $row->return_number,
                date: $row->return_date?->format('Y-m-d') ?? '-',
                party: $row->supplier?->name ?? '-',
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->grand_total,
                quantity: null,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function stockTransfers(string $fromDate, string $toDate, string $status): Collection
    {
        return StockTransfer::query()
            ->with(['fromWarehouse', 'toWarehouse', 'fromBranch', 'user'])
            ->whereDate('transfer_date', '>=', $fromDate)
            ->whereDate('transfer_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (StockTransfer $row): array => $this->row(
                id: $row->id,
                typeKey: 'stock_transfer',
                typeLabel: 'تحويل مخزني',
                number: $row->transfer_number,
                date: $row->transfer_date?->format('Y-m-d') ?? '-',
                party: ($row->fromWarehouse?->name ?? '-') . ' ← ' . ($row->toWarehouse?->name ?? '-'),
                branch: $row->fromBranch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->total_cost,
                quantity: (float) $row->total_quantity,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function stockCounts(string $fromDate, string $toDate, string $status): Collection
    {
        return StockCountDocument::query()
            ->with(['warehouse', 'branch', 'user'])
            ->whereDate('count_date', '>=', $fromDate)
            ->whereDate('count_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (StockCountDocument $row): array => $this->row(
                id: $row->id,
                typeKey: 'stock_count',
                typeLabel: 'جرد مخزني',
                number: $row->count_number,
                date: $row->count_date?->format('Y-m-d') ?? '-',
                party: $row->warehouse?->name ?? '-',
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->total_difference_cost,
                quantity: (float) $row->total_increase_quantity - (float) $row->total_decrease_quantity,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function damagedStockDocuments(string $fromDate, string $toDate, string $status): Collection
    {
        return DamagedStockDocument::query()
            ->with(['warehouse', 'branch', 'user'])
            ->whereDate('document_date', '>=', $fromDate)
            ->whereDate('document_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (DamagedStockDocument $row): array => $this->row(
                id: $row->id,
                typeKey: 'damaged_stock',
                typeLabel: 'تالف مخزني',
                number: $row->document_number,
                date: $row->document_date?->format('Y-m-d') ?? '-',
                party: $row->warehouse?->name ?? '-',
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->total_cost,
                quantity: (float) $row->total_quantity,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function receiptVouchers(string $fromDate, string $toDate, string $status): Collection
    {
        return ReceiptVoucher::query()
            ->with(['branch', 'user'])
            ->whereDate('voucher_date', '>=', $fromDate)
            ->whereDate('voucher_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (ReceiptVoucher $row): array => $this->row(
                id: $row->id,
                typeKey: 'receipt_voucher',
                typeLabel: 'سند قبض',
                number: $row->voucher_number,
                date: $row->voucher_date?->format('Y-m-d') ?? '-',
                party: method_exists($row, 'resolvedPartyName') ? $row->resolvedPartyName() : ($row->party_name ?? '-'),
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->amount,
                quantity: null,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function paymentVouchers(string $fromDate, string $toDate, string $status): Collection
    {
        return PaymentVoucher::query()
            ->with(['branch', 'user'])
            ->whereDate('voucher_date', '>=', $fromDate)
            ->whereDate('voucher_date', '<=', $toDate)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->get()
            ->map(fn (PaymentVoucher $row): array => $this->row(
                id: $row->id,
                typeKey: 'payment_voucher',
                typeLabel: 'سند صرف',
                number: $row->voucher_number,
                date: $row->voucher_date?->format('Y-m-d') ?? '-',
                party: method_exists($row, 'resolvedPartyName') ? $row->resolvedPartyName() : ($row->party_name ?? '-'),
                branch: $row->branch?->name ?? '-',
                user: $row->user?->name ?? '-',
                status: $row->status,
                amount: (float) $row->amount,
                quantity: null,
                postedAt: $row->posted_at?->format('Y-m-d H:i') ?? '-'
            ));
    }

    private function row(
        int $id,
        string $typeKey,
        string $typeLabel,
        ?string $number,
        string $date,
        string $party,
        string $branch,
        string $user,
        ?string $status,
        float $amount,
        ?float $quantity,
        string $postedAt
    ): array {
        return [
            'id' => $id,
            'document_type_key' => $typeKey,
            'document_type_label' => $typeLabel,
            'document_number' => $number ?: '-',
            'document_date' => $date,
            'party' => $party,
            'branch' => $branch,
            'user' => $user,
            'status' => $status ?: '-',
            'status_label' => $this->statusLabel($status),
            'amount' => $amount,
            'quantity' => $quantity,
            'posted_at' => $postedAt,
        ];
    }

    private function totals(Collection $rows): array
    {
        return [
            'documents_count' => $rows->count(),
            'posted_count' => $rows->where('status', 'posted')->count(),
            'draft_count' => $rows->where('status', 'draft')->count(),
            'total_amount' => $rows->sum(fn (array $row): float => (float) $row['amount']),
            'total_quantity' => $rows->sum(fn (array $row): float => (float) ($row['quantity'] ?? 0)),
        ];
    }

    private function byDocumentType(Collection $rows): array
    {
        return $rows
            ->groupBy('document_type_key')
            ->map(fn (Collection $group): array => [
                'document_type_key' => $group->first()['document_type_key'],
                'document_type_label' => $group->first()['document_type_label'],
                'documents_count' => $group->count(),
                'posted_count' => $group->where('status', 'posted')->count(),
                'draft_count' => $group->where('status', 'draft')->count(),
                'total_amount' => $group->sum(fn (array $row): float => (float) $row['amount']),
            ])
            ->sortByDesc('documents_count')
            ->values()
            ->toArray();
    }

    private function byStatus(Collection $rows): array
    {
        return $rows
            ->groupBy('status')
            ->map(fn (Collection $group): array => [
                'status' => $group->first()['status'],
                'status_label' => $this->statusLabel($group->first()['status']),
                'documents_count' => $group->count(),
                'total_amount' => $group->sum(fn (array $row): float => (float) $row['amount']),
            ])
            ->values()
            ->toArray();
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'posted' => 'مرحّل',
            'draft' => 'مسودة',
            default => $status ?: '-',
        };
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