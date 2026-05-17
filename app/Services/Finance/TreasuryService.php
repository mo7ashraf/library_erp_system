<?php

namespace App\Services\Finance;

use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\TreasuryTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TreasuryService
{
    public function receive(array $data): TreasuryTransaction
    {
        return $this->record($data, TreasuryTransaction::DIRECTION_IN);
    }

    public function pay(array $data): TreasuryTransaction
    {
        return $this->record($data, TreasuryTransaction::DIRECTION_OUT);
    }

    public function record(array $data, string $direction): TreasuryTransaction
    {
        return DB::transaction(function () use ($data, $direction) {
            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new RuntimeException('المبلغ يجب أن يكون أكبر من صفر.');
            }

            $channel = $data['payment_channel'];

            if (! in_array($channel, [TreasuryTransaction::CHANNEL_CASH, TreasuryTransaction::CHANNEL_BANK], true)) {
                throw new RuntimeException('طريقة السداد غير صحيحة.');
            }

            $cashbox = null;
            $bankAccount = null;
            $currentBalance = 0;

            if ($channel === TreasuryTransaction::CHANNEL_CASH) {
                if (empty($data['cashbox_id'])) {
                    throw new RuntimeException('يجب اختيار الخزينة.');
                }

                $cashbox = Cashbox::findOrFail($data['cashbox_id']);
                $currentBalance = (float) $cashbox->current_balance;
            }

            if ($channel === TreasuryTransaction::CHANNEL_BANK) {
                if (empty($data['bank_account_id'])) {
                    throw new RuntimeException('يجب اختيار الحساب البنكي.');
                }

                $bankAccount = BankAccount::findOrFail($data['bank_account_id']);
                $currentBalance = (float) $bankAccount->current_balance;
            }

            if ($direction === TreasuryTransaction::DIRECTION_OUT && $currentBalance < $amount) {
                throw new RuntimeException('الرصيد غير كافٍ لإتمام عملية الصرف.');
            }

            $newBalance = $direction === TreasuryTransaction::DIRECTION_IN
                ? $currentBalance + $amount
                : $currentBalance - $amount;

            if ($cashbox) {
                $cashbox->update([
                    'current_balance' => $newBalance,
                ]);
            }

            if ($bankAccount) {
                $bankAccount->update([
                    'current_balance' => $newBalance,
                ]);
            }

            return TreasuryTransaction::create([
                'branch_id' => $data['branch_id'] ?? $cashbox?->branch_id ?? $bankAccount?->branch_id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'cashbox_id' => $cashbox?->id,
                'bank_account_id' => $bankAccount?->id,
                'transaction_number' => $data['transaction_number'],
                'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                'payment_channel' => $channel,
                'direction' => $direction,
                'transaction_type' => $data['transaction_type'],
                'party_type' => $data['party_type'] ?? null,
                'party_id' => $data['party_id'] ?? null,
                'party_name' => $data['party_name'] ?? null,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $data['description'] ?? null,
            ]);
        });
    }
}