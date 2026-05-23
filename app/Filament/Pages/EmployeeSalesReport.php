<?php

namespace App\Filament\Pages;

use App\Models\SalesInvoice;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class EmployeeSalesReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.pages.employee-sales-report';

    public ?int $employeeId = null;

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $employees = [];

    public array $summaryRows = [];

    public array $invoiceRows = [];

    public float $totalSales = 0;

    public int $invoicesCount = 0;

    public float $averageInvoiceValue = 0;

    public static function getNavigationLabel(): string
    {
        return 'مبيعات الموظفين';
    }

    public function getTitle(): string
    {
        return 'تقرير مبيعات الموظفين';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function mount(): void
    {
        $this->fromDate = now()->toDateString();
        $this->toDate = now()->toDateString();

        $this->employees = User::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->loadReport();
    }

    public function loadReport(): void
    {
        $invoices = SalesInvoice::query()
            ->with(['user', 'customer', 'warehouse'])
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->when($this->employeeId, fn ($query) => $query->where('user_id', $this->employeeId))
            ->when($this->fromDate, fn ($query) => $query->whereDate('invoice_date', '>=', $this->fromDate))
            ->when($this->toDate, fn ($query) => $query->whereDate('invoice_date', '<=', $this->toDate))
            ->latest('id')
            ->get();

        $this->totalSales = (float) $invoices->sum('grand_total');
        $this->invoicesCount = $invoices->count();
        $this->averageInvoiceValue = $this->invoicesCount > 0
            ? $this->totalSales / $this->invoicesCount
            : 0;

        $this->summaryRows = $invoices
            ->groupBy('user_id')
            ->map(function ($employeeInvoices): array {
                $first = $employeeInvoices->first();

                $total = (float) $employeeInvoices->sum('grand_total');
                $count = $employeeInvoices->count();

                return [
                    'employee_name' => $first?->user?->name ?? 'غير محدد',
                    'invoices_count' => $count,
                    'total_sales' => $total,
                    'average_invoice_value' => $count > 0 ? $total / $count : 0,
                    'cash_total' => (float) $employeeInvoices
                        ->where('payment_type', SalesInvoice::PAYMENT_CASH)
                        ->sum('grand_total'),
                    'partial_total' => (float) $employeeInvoices
                        ->where('payment_type', SalesInvoice::PAYMENT_PARTIAL)
                        ->sum('grand_total'),
                    'credit_total' => (float) $employeeInvoices
                        ->where('payment_type', SalesInvoice::PAYMENT_CREDIT)
                        ->sum('grand_total'),
                ];
            })
            ->sortByDesc('total_sales')
            ->values()
            ->toArray();

        $this->invoiceRows = $invoices
            ->map(function (SalesInvoice $invoice): array {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date?->format('Y-m-d') ?? '-',
                    'employee_name' => $invoice->user?->name ?? 'غير محدد',
                    'customer_name' => $invoice->customer?->name ?? '-',
                    'warehouse_name' => $invoice->warehouse?->name ?? '-',
                    'payment_type' => $this->paymentTypeLabel($invoice->payment_type),
                    'subtotal' => (float) $invoice->subtotal,
                    'discount_amount' => (float) $invoice->discount_amount,
                    'grand_total' => (float) $invoice->grand_total,
                    'posted_at' => $invoice->posted_at?->format('Y-m-d H:i') ?? '-',
                ];
            })
            ->toArray();
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