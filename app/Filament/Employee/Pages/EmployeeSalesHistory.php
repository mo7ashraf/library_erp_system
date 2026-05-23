<?php

namespace App\Filament\Employee\Pages;

use App\Models\SalesInvoice;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class EmployeeSalesHistory extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|UnitEnum|null $navigationGroup = 'نقطة البيع';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.employee.pages.employee-sales-history';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $rows = [];

    public float $totalSales = 0;

    public int $invoicesCount = 0;

    public static function getNavigationLabel(): string
    {
        return 'مبيعاتي';
    }

    public function getTitle(): string
    {
        return 'مبيعاتي';
    }

    public function mount(): void
    {
        $this->fromDate = now()->toDateString();
        $this->toDate = now()->toDateString();

        $this->loadSales();
    }

    public function loadSales(): void
    {
        $query = SalesInvoice::query()
            ->with(['customer', 'warehouse'])
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->where('user_id', auth()->id());

        if ($this->fromDate) {
            $query->whereDate('invoice_date', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('invoice_date', '<=', $this->toDate);
        }

        $invoices = $query
            ->latest('id')
            ->get();

        $this->rows = $invoices
            ->map(function (SalesInvoice $invoice): array {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date?->format('Y-m-d') ?? '-',
                    'customer' => $invoice->customer?->name ?? '-',
                    'warehouse' => $invoice->warehouse?->name ?? '-',
                    'payment_type' => $this->paymentTypeLabel($invoice->payment_type),
                    'grand_total' => (float) $invoice->grand_total,
                    'posted_at' => $invoice->posted_at?->format('Y-m-d H:i') ?? '-',
                ];
            })
            ->toArray();

        $this->totalSales = (float) $invoices->sum('grand_total');
        $this->invoicesCount = $invoices->count();
    }

    private function paymentTypeLabel(?string $paymentType): string
    {
        return match ($paymentType) {
            SalesInvoice::PAYMENT_CASH => 'دفع كامل',
            SalesInvoice::PAYMENT_PARTIAL => 'دفع جزئي',
            SalesInvoice::PAYMENT_CREDIT => 'آجل',
            default => '-',
        };
    }
}