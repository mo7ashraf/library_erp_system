<?php

namespace App\Services\Reports;

use App\Models\SalesInvoice;
use Carbon\Carbon;

class SalesReportService
{
    public function summary(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $this->normalizeDate($fromDate) ?: now()->startOfMonth()->toDateString();
        $toDate = $this->normalizeDate($toDate) ?: now()->toDateString();

        $baseQuery = SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
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
                'service_amount' => (float) (clone $baseQuery)->sum('service_amount'),
                'commission_amount' => (float) (clone $baseQuery)->sum('commission_amount'),
                'grand_total' => $grandTotal,
                'average_invoice_value' => $invoicesCount > 0 ? $grandTotal / $invoicesCount : 0,
                'cash_total' => $this->paymentTypeTotal($fromDate, $toDate, SalesInvoice::PAYMENT_CASH),
                'credit_total' => $this->paymentTypeTotal($fromDate, $toDate, SalesInvoice::PAYMENT_CREDIT),
                'partial_total' => $this->paymentTypeTotal($fromDate, $toDate, SalesInvoice::PAYMENT_PARTIAL),
            ],

            'sales_by_payment_type' => $this->salesByPaymentType($fromDate, $toDate),
            'sales_by_price_type' => $this->salesByPriceType($fromDate, $toDate),
            'top_customers' => $this->topCustomers($fromDate, $toDate),
            'latest_invoices' => $this->latestInvoices($fromDate, $toDate),
        ];
    }

    private function paymentTypeTotal(string $fromDate, string $toDate, string $paymentType): float
    {
        return (float) SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->where('payment_type', $paymentType)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->sum('grand_total');
    }

    private function salesByPaymentType(string $fromDate, string $toDate): array
    {
        return SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->selectRaw('payment_type, COUNT(*) as invoices_count, SUM(grand_total) as total_sales')
            ->groupBy('payment_type')
            ->orderByDesc('total_sales')
            ->get()
            ->map(fn (SalesInvoice $row): array => [
                'payment_type' => $row->payment_type,
                'payment_type_label' => $this->paymentTypeLabel($row->payment_type),
                'invoices_count' => (int) $row->invoices_count,
                'total_sales' => (float) $row->total_sales,
            ])
            ->values()
            ->toArray();
    }

    private function salesByPriceType(string $fromDate, string $toDate): array
    {
        return SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->selectRaw('price_type, COUNT(*) as invoices_count, SUM(grand_total) as total_sales')
            ->groupBy('price_type')
            ->orderByDesc('total_sales')
            ->get()
            ->map(fn (SalesInvoice $row): array => [
                'price_type' => $row->price_type,
                'price_type_label' => $this->priceTypeLabel($row->price_type),
                'invoices_count' => (int) $row->invoices_count,
                'total_sales' => (float) $row->total_sales,
            ])
            ->values()
            ->toArray();
    }

    private function topCustomers(string $fromDate, string $toDate): array
    {
        return SalesInvoice::query()
            ->with('customer')
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->selectRaw('customer_id, COUNT(*) as invoices_count, SUM(grand_total) as total_sales')
            ->groupBy('customer_id')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get()
            ->map(fn (SalesInvoice $row): array => [
                'customer_id' => $row->customer_id,
                'customer_name' => $row->customer?->name ?? '-',
                'invoices_count' => (int) $row->invoices_count,
                'total_sales' => (float) $row->total_sales,
            ])
            ->values()
            ->toArray();
    }

    private function latestInvoices(string $fromDate, string $toDate): array
    {
        return SalesInvoice::query()
            ->with(['customer', 'warehouse', 'branch'])
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->latest('invoice_date')
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(fn (SalesInvoice $invoice): array => [
                'date' => $invoice->invoice_date?->format('Y-m-d') ?? '-',
                'number' => $invoice->invoice_number,
                'customer' => $invoice->customer?->name ?? '-',
                'warehouse' => $invoice->warehouse?->name ?? '-',
                'branch' => $invoice->branch?->name ?? '-',
                'payment_type' => $this->paymentTypeLabel($invoice->payment_type),
                'price_type' => $this->priceTypeLabel($invoice->price_type),
                'subtotal' => (float) $invoice->subtotal,
                'discount_amount' => (float) $invoice->discount_amount,
                'grand_total' => (float) $invoice->grand_total,
            ])
            ->values()
            ->toArray();
    }

    private function paymentTypeLabel(?string $state): string
    {
        return match ($state) {
            SalesInvoice::PAYMENT_CASH => 'نقدي',
            SalesInvoice::PAYMENT_CREDIT => 'آجل',
            SalesInvoice::PAYMENT_PARTIAL => 'جزئي',
            default => '-',
        };
    }

    private function priceTypeLabel(?string $state): string
    {
        return match ($state) {
            SalesInvoice::PRICE_STUDENT => 'طالب',
            SalesInvoice::PRICE_TEACHER => 'معلم',
            SalesInvoice::PRICE_REPRESENTATIVE => 'مندوب',
            SalesInvoice::PRICE_RETAIL => 'قطاعي',
            SalesInvoice::PRICE_WHOLESALE => 'جملة',
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