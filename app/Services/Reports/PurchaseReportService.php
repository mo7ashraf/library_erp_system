<?php

namespace App\Services\Reports;

use App\Models\PurchaseInvoice;
use Carbon\Carbon;

class PurchaseReportService
{
    public function summary(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $this->normalizeDate($fromDate) ?: now()->startOfMonth()->toDateString();
        $toDate = $this->normalizeDate($toDate) ?: now()->toDateString();

        $baseQuery = PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate);

        $invoicesCount = (clone $baseQuery)->count();
        $grandTotal = (float) (clone $baseQuery)->sum('grand_total');

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,

            'totals' => [
                'invoices_count' => $invoicesCount,
                'subtotal' => (float) (clone $baseQuery)->sum('subtotal'),
                'discount_amount' => (float) (clone $baseQuery)->sum('discount_amount'),
                'additional_cost' => (float) (clone $baseQuery)->sum('additional_cost'),
                'grand_total' => $grandTotal,
                'average_invoice_value' => $invoicesCount > 0 ? $grandTotal / $invoicesCount : 0,
                'cash_total' => $this->paymentTypeTotal($fromDate, $toDate, PurchaseInvoice::PAYMENT_CASH),
                'credit_total' => $this->paymentTypeTotal($fromDate, $toDate, PurchaseInvoice::PAYMENT_CREDIT),
                'partial_total' => $this->paymentTypeTotal($fromDate, $toDate, PurchaseInvoice::PAYMENT_PARTIAL),
            ],

            'purchases_by_payment_type' => $this->purchasesByPaymentType($fromDate, $toDate),
            'top_suppliers' => $this->topSuppliers($fromDate, $toDate),
            'purchases_by_warehouse' => $this->purchasesByWarehouse($fromDate, $toDate),
            'latest_invoices' => $this->latestInvoices($fromDate, $toDate),
        ];
    }

    private function paymentTypeTotal(string $fromDate, string $toDate, string $paymentType): float
    {
        return (float) PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->where('payment_type', $paymentType)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->sum('grand_total');
    }

    private function purchasesByPaymentType(string $fromDate, string $toDate): array
    {
        return PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->selectRaw('payment_type, COUNT(*) as invoices_count, SUM(grand_total) as total_purchases')
            ->groupBy('payment_type')
            ->orderByDesc('total_purchases')
            ->get()
            ->map(fn (PurchaseInvoice $row): array => [
                'payment_type' => $row->payment_type,
                'payment_type_label' => $this->paymentTypeLabel($row->payment_type),
                'invoices_count' => (int) $row->invoices_count,
                'total_purchases' => (float) $row->total_purchases,
            ])
            ->values()
            ->toArray();
    }

    private function topSuppliers(string $fromDate, string $toDate): array
    {
        return PurchaseInvoice::query()
            ->with('supplier')
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->selectRaw('supplier_id, COUNT(*) as invoices_count, SUM(grand_total) as total_purchases')
            ->groupBy('supplier_id')
            ->orderByDesc('total_purchases')
            ->limit(10)
            ->get()
            ->map(fn (PurchaseInvoice $row): array => [
                'supplier_id' => $row->supplier_id,
                'supplier_name' => $row->supplier?->name ?? '-',
                'invoices_count' => (int) $row->invoices_count,
                'total_purchases' => (float) $row->total_purchases,
            ])
            ->values()
            ->toArray();
    }

    private function purchasesByWarehouse(string $fromDate, string $toDate): array
    {
        return PurchaseInvoice::query()
            ->with('warehouse')
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->selectRaw('warehouse_id, COUNT(*) as invoices_count, SUM(grand_total) as total_purchases')
            ->groupBy('warehouse_id')
            ->orderByDesc('total_purchases')
            ->get()
            ->map(fn (PurchaseInvoice $row): array => [
                'warehouse_id' => $row->warehouse_id,
                'warehouse_name' => $row->warehouse?->name ?? '-',
                'invoices_count' => (int) $row->invoices_count,
                'total_purchases' => (float) $row->total_purchases,
            ])
            ->values()
            ->toArray();
    }

    private function latestInvoices(string $fromDate, string $toDate): array
    {
        return PurchaseInvoice::query()
            ->with(['supplier', 'warehouse', 'branch'])
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->latest('invoice_date')
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(fn (PurchaseInvoice $invoice): array => [
                'date' => $invoice->invoice_date?->format('Y-m-d') ?? '-',
                'number' => $invoice->invoice_number,
                'supplier_invoice_number' => $invoice->supplier_invoice_number ?? '-',
                'supplier' => $invoice->supplier?->name ?? '-',
                'warehouse' => $invoice->warehouse?->name ?? '-',
                'branch' => $invoice->branch?->name ?? '-',
                'payment_type' => $this->paymentTypeLabel($invoice->payment_type),
                'subtotal' => (float) $invoice->subtotal,
                'discount_amount' => (float) $invoice->discount_amount,
                'additional_cost' => (float) $invoice->additional_cost,
                'grand_total' => (float) $invoice->grand_total,
            ])
            ->values()
            ->toArray();
    }

    private function paymentTypeLabel(?string $state): string
    {
        return match ($state) {
            PurchaseInvoice::PAYMENT_CASH => 'نقدي',
            PurchaseInvoice::PAYMENT_CREDIT => 'آجل',
            PurchaseInvoice::PAYMENT_PARTIAL => 'جزئي',
            default => '-',
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