<?php

namespace App\Services\Dashboard;

use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\TreasuryTransaction;
use App\Models\WarehouseItemBalance;
use Carbon\Carbon;

class ErpDashboardService
{
    public function summary(): array
    {
        $today = Carbon::today()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();

        return [
            'periods' => [
                'today' => $today,
                'month_start' => $monthStart,
                'month_end' => $monthEnd,
            ],

            'sales' => [
                'today_total' => $this->salesTotal($today, $today),
                'today_count' => $this->salesCount($today, $today),
                'month_total' => $this->salesTotal($monthStart, $monthEnd),
                'month_count' => $this->salesCount($monthStart, $monthEnd),
                'latest' => $this->latestSalesInvoices(),
            ],

            'purchases' => [
                'today_total' => $this->purchaseTotal($today, $today),
                'today_count' => $this->purchaseCount($today, $today),
                'month_total' => $this->purchaseTotal($monthStart, $monthEnd),
                'month_count' => $this->purchaseCount($monthStart, $monthEnd),
                'latest' => $this->latestPurchaseInvoices(),
            ],

            'treasury' => [
                'today_inflow' => $this->treasuryTotal(TreasuryTransaction::DIRECTION_IN, $today, $today),
                'today_outflow' => $this->treasuryTotal(TreasuryTransaction::DIRECTION_OUT, $today, $today),
                'month_inflow' => $this->treasuryTotal(TreasuryTransaction::DIRECTION_IN, $monthStart, $monthEnd),
                'month_outflow' => $this->treasuryTotal(TreasuryTransaction::DIRECTION_OUT, $monthStart, $monthEnd),
                'cashbox_balance' => (float) Cashbox::query()->sum('current_balance'),
                'bank_balance' => (float) BankAccount::query()->sum('current_balance'),
                'latest' => $this->latestTreasuryTransactions(),
            ],

            'inventory' => [
                'total_quantity' => (float) WarehouseItemBalance::query()->sum('quantity'),
                'total_value' => (float) WarehouseItemBalance::query()->sum('total_cost'),
                'items_count' => WarehouseItemBalance::query()
                    ->where('quantity', '>', 0)
                    ->distinct('item_id')
                    ->count('item_id'),
                'top_value_items' => $this->topInventoryValueItems(),
            ],
        ];
    }

    private function salesTotal(string $fromDate, string $toDate): float
    {
        return (float) SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->sum('grand_total');
    }

    private function salesCount(string $fromDate, string $toDate): int
    {
        return SalesInvoice::query()
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->count();
    }

    private function purchaseTotal(string $fromDate, string $toDate): float
    {
        return (float) PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->sum('grand_total');
    }

    private function purchaseCount(string $fromDate, string $toDate): int
    {
        return PurchaseInvoice::query()
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->whereDate('invoice_date', '>=', $fromDate)
            ->whereDate('invoice_date', '<=', $toDate)
            ->count();
    }

    private function treasuryTotal(string $direction, string $fromDate, string $toDate): float
    {
        return (float) TreasuryTransaction::query()
            ->where('direction', $direction)
            ->whereDate('transaction_date', '>=', $fromDate)
            ->whereDate('transaction_date', '<=', $toDate)
            ->sum('amount');
    }

    private function latestSalesInvoices(): array
    {
        return SalesInvoice::query()
            ->with(['customer'])
            ->where('status', SalesInvoice::STATUS_POSTED)
            ->latest('invoice_date')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (SalesInvoice $invoice): array => [
                'date' => $invoice->invoice_date?->format('Y-m-d') ?? '-',
                'number' => $invoice->invoice_number,
                'party' => $invoice->customer?->name ?? '-',
                'amount' => (float) $invoice->grand_total,
                'status' => $invoice->status,
            ])
            ->values()
            ->toArray();
    }

    private function latestPurchaseInvoices(): array
    {
        return PurchaseInvoice::query()
            ->with(['supplier'])
            ->where('status', PurchaseInvoice::STATUS_POSTED)
            ->latest('invoice_date')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (PurchaseInvoice $invoice): array => [
                'date' => $invoice->invoice_date?->format('Y-m-d') ?? '-',
                'number' => $invoice->invoice_number,
                'party' => $invoice->supplier?->name ?? '-',
                'amount' => (float) $invoice->grand_total,
                'status' => $invoice->status,
            ])
            ->values()
            ->toArray();
    }

    private function latestTreasuryTransactions(): array
    {
        return TreasuryTransaction::query()
            ->with(['cashbox', 'bankAccount'])
            ->latest('transaction_date')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (TreasuryTransaction $transaction): array => [
                'date' => $transaction->transaction_date?->format('Y-m-d') ?? '-',
                'number' => $transaction->transaction_number,
                'type' => $this->transactionTypeLabel($transaction->transaction_type),
                'direction' => $transaction->direction,
                'direction_label' => $this->directionLabel($transaction->direction),
                'account' => $transaction->cashbox?->name
                    ?? $transaction->bankAccount?->account_name
                    ?? '-',
                'party' => $transaction->party_name ?? '-',
                'amount' => (float) $transaction->amount,
                'balance_after' => (float) $transaction->balance_after,
            ])
            ->values()
            ->toArray();
    }

    private function topInventoryValueItems(): array
    {
        return WarehouseItemBalance::query()
            ->with(['item', 'warehouse'])
            ->where('quantity', '>', 0)
            ->orderByDesc('total_cost')
            ->limit(8)
            ->get()
            ->map(fn (WarehouseItemBalance $balance): array => [
                'item' => $balance->item?->name ?? '-',
                'warehouse' => $balance->warehouse?->name ?? '-',
                'quantity' => (float) $balance->quantity,
                'average_cost' => (float) $balance->average_cost,
                'total_cost' => (float) $balance->total_cost,
            ])
            ->values()
            ->toArray();
    }

    private function directionLabel(?string $state): string
    {
        return match ($state) {
            TreasuryTransaction::DIRECTION_IN => 'داخل',
            TreasuryTransaction::DIRECTION_OUT => 'خارج',
            default => '-',
        };
    }

    private function transactionTypeLabel(?string $state): string
    {
        return match ($state) {
            TreasuryTransaction::TYPE_OPENING_BALANCE => 'رصيد افتتاحي',
            TreasuryTransaction::TYPE_CUSTOMER_RECEIPT => 'تحصيل من عميل',
            TreasuryTransaction::TYPE_SUPPLIER_PAYMENT => 'دفعة لمورد',
            TreasuryTransaction::TYPE_EXPENSE => 'مصروف',
            TreasuryTransaction::TYPE_INCOME => 'إيراد',
            TreasuryTransaction::TYPE_SALES_INVOICE => 'فاتورة مبيعات',
            TreasuryTransaction::TYPE_PURCHASE_INVOICE => 'فاتورة مشتريات',
            TreasuryTransaction::TYPE_SALES_RETURN => 'مرتجع مبيعات',
            TreasuryTransaction::TYPE_PURCHASE_RETURN => 'مرتجع مشتريات',
            TreasuryTransaction::TYPE_MANUAL_ADJUSTMENT => 'تسوية يدوية',
            default => $state ?: '-',
        };
    }
}