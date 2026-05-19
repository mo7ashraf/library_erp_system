<?php

namespace App\Services\Finance;

use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\TreasuryTransaction;
use Carbon\Carbon;

class FinancialReportService
{
    public function summary(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $this->normalizeDate($fromDate);
        $toDate = $this->normalizeDate($toDate);

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,

            'total_inflow' => $this->sumTransactions(TreasuryTransaction::DIRECTION_IN, $fromDate, $toDate),
            'total_outflow' => $this->sumTransactions(TreasuryTransaction::DIRECTION_OUT, $fromDate, $toDate),

            'cash_inflow' => $this->sumTransactions(TreasuryTransaction::DIRECTION_IN, $fromDate, $toDate, TreasuryTransaction::CHANNEL_CASH),
            'cash_outflow' => $this->sumTransactions(TreasuryTransaction::DIRECTION_OUT, $fromDate, $toDate, TreasuryTransaction::CHANNEL_CASH),

            'bank_inflow' => $this->sumTransactions(TreasuryTransaction::DIRECTION_IN, $fromDate, $toDate, TreasuryTransaction::CHANNEL_BANK),
            'bank_outflow' => $this->sumTransactions(TreasuryTransaction::DIRECTION_OUT, $fromDate, $toDate, TreasuryTransaction::CHANNEL_BANK),

            'cashbox_total_balance' => (float) Cashbox::query()->sum('current_balance'),
            'bank_total_balance' => (float) BankAccount::query()->sum('current_balance'),

            'transaction_type_summary' => $this->transactionTypeSummary($fromDate, $toDate),
            'cashboxes' => $this->cashboxBalances($fromDate, $toDate),
            'bank_accounts' => $this->bankAccountBalances($fromDate, $toDate),
            'latest_transactions' => $this->latestTransactions($fromDate, $toDate),
        ];
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

    private function transactionTypeSummary(?string $fromDate, ?string $toDate): array
    {
        return TreasuryTransaction::query()
            ->when($fromDate, fn ($query) => $query->whereDate('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('transaction_date', '<=', $toDate))
            ->selectRaw('transaction_type, direction, COUNT(*) as transactions_count, SUM(amount) as total_amount')
            ->groupBy('transaction_type', 'direction')
            ->orderBy('transaction_type')
            ->get()
            ->map(fn (TreasuryTransaction $row): array => [
                'transaction_type' => $row->transaction_type,
                'transaction_type_label' => $this->transactionTypeLabel($row->transaction_type),
                'direction' => $row->direction,
                'direction_label' => $this->directionLabel($row->direction),
                'transactions_count' => (int) $row->transactions_count,
                'total_amount' => (float) $row->total_amount,
            ])
            ->values()
            ->toArray();
    }

    private function cashboxBalances(?string $fromDate, ?string $toDate): array
    {
        return Cashbox::query()
            ->with('branch')
            ->orderBy('name')
            ->get()
            ->map(function (Cashbox $cashbox) use ($fromDate, $toDate): array {
                $periodIn = $this->sumAccountTransactions(
                    TreasuryTransaction::CHANNEL_CASH,
                    'cashbox_id',
                    $cashbox->id,
                    TreasuryTransaction::DIRECTION_IN,
                    $fromDate,
                    $toDate
                );

                $periodOut = $this->sumAccountTransactions(
                    TreasuryTransaction::CHANNEL_CASH,
                    'cashbox_id',
                    $cashbox->id,
                    TreasuryTransaction::DIRECTION_OUT,
                    $fromDate,
                    $toDate
                );

                return [
                    'name' => $cashbox->name,
                    'code' => $cashbox->code,
                    'branch_name' => $cashbox->branch?->name ?? '-',
                    'opening_balance' => (float) $cashbox->opening_balance,
                    'period_in' => $periodIn,
                    'period_out' => $periodOut,
                    'period_net' => $periodIn - $periodOut,
                    'current_balance' => (float) $cashbox->current_balance,
                    'is_active' => (bool) $cashbox->is_active,
                ];
            })
            ->values()
            ->toArray();
    }

    private function bankAccountBalances(?string $fromDate, ?string $toDate): array
    {
        return BankAccount::query()
            ->with('branch')
            ->orderBy('bank_name')
            ->orderBy('account_name')
            ->get()
            ->map(function (BankAccount $bankAccount) use ($fromDate, $toDate): array {
                $periodIn = $this->sumAccountTransactions(
                    TreasuryTransaction::CHANNEL_BANK,
                    'bank_account_id',
                    $bankAccount->id,
                    TreasuryTransaction::DIRECTION_IN,
                    $fromDate,
                    $toDate
                );

                $periodOut = $this->sumAccountTransactions(
                    TreasuryTransaction::CHANNEL_BANK,
                    'bank_account_id',
                    $bankAccount->id,
                    TreasuryTransaction::DIRECTION_OUT,
                    $fromDate,
                    $toDate
                );

                return [
                    'bank_name' => $bankAccount->bank_name,
                    'account_name' => $bankAccount->account_name,
                    'account_number' => $bankAccount->account_number,
                    'code' => $bankAccount->code,
                    'branch_name' => $bankAccount->branch?->name ?? '-',
                    'opening_balance' => (float) $bankAccount->opening_balance,
                    'period_in' => $periodIn,
                    'period_out' => $periodOut,
                    'period_net' => $periodIn - $periodOut,
                    'current_balance' => (float) $bankAccount->current_balance,
                    'is_active' => (bool) $bankAccount->is_active,
                ];
            })
            ->values()
            ->toArray();
    }

    private function sumAccountTransactions(
        string $paymentChannel,
        string $accountColumn,
        int $accountId,
        string $direction,
        ?string $fromDate,
        ?string $toDate
    ): float {
        return (float) TreasuryTransaction::query()
            ->where('payment_channel', $paymentChannel)
            ->where($accountColumn, $accountId)
            ->where('direction', $direction)
            ->when($fromDate, fn ($query) => $query->whereDate('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('transaction_date', '<=', $toDate))
            ->sum('amount');
    }

    private function latestTransactions(?string $fromDate, ?string $toDate): array
    {
        return TreasuryTransaction::query()
            ->with(['cashbox', 'bankAccount'])
            ->when($fromDate, fn ($query) => $query->whereDate('transaction_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('transaction_date', '<=', $toDate))
            ->latest('transaction_date')
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(fn (TreasuryTransaction $transaction): array => [
                'transaction_date' => $transaction->transaction_date?->format('Y-m-d') ?? '-',
                'transaction_number' => $transaction->transaction_number,
                'payment_channel_label' => $this->paymentChannelLabel($transaction->payment_channel),
                'account_name' => $transaction->cashbox?->name
                    ?? $transaction->bankAccount?->account_name
                    ?? '-',
                'direction' => $transaction->direction,
                'direction_label' => $this->directionLabel($transaction->direction),
                'transaction_type_label' => $this->transactionTypeLabel($transaction->transaction_type),
                'party_name' => $transaction->party_name ?? '-',
                'reference_number' => $transaction->reference_number ?? '-',
                'amount' => (float) $transaction->amount,
                'balance_after' => (float) $transaction->balance_after,
            ])
            ->values()
            ->toArray();
    }

    private function paymentChannelLabel(?string $state): string
    {
        return match ($state) {
            TreasuryTransaction::CHANNEL_CASH => 'خزينة',
            TreasuryTransaction::CHANNEL_BANK => 'بنك',
            default => '-',
        };
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