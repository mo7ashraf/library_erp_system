<?php

namespace App\Filament\Resources\PaymentVouchers\Pages;

use App\Filament\Resources\PaymentVouchers\PaymentVoucherResource;
use App\Models\Customer;
use App\Models\PaymentVoucher;
use App\Models\Supplier;
use App\Models\TreasuryTransaction;
use App\Services\Finance\TreasuryService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePaymentVoucher extends CreateRecord
{
    protected static string $resource = PaymentVoucherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = PaymentVoucher::STATUS_DRAFT;

        match ($data['voucher_type'] ?? PaymentVoucher::TYPE_SUPPLIER_PAYMENT) {
            PaymentVoucher::TYPE_SUPPLIER_PAYMENT => $data['party_type'] = PaymentVoucher::PARTY_SUPPLIER,
            PaymentVoucher::TYPE_CUSTOMER_REFUND => $data['party_type'] = PaymentVoucher::PARTY_CUSTOMER,
            default => $data['party_type'] = PaymentVoucher::PARTY_OTHER,
        };

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            if ($this->record->status === PaymentVoucher::STATUS_POSTED) {
                return;
            }

            $this->record->load(['category']);

            $partyName = $this->resolvePartyName();

            $transaction = app(TreasuryService::class)->pay([
                'branch_id' => $this->record->branch_id,
                'user_id' => auth()->id(),
                'cashbox_id' => $this->record->cashbox_id,
                'bank_account_id' => $this->record->bank_account_id,
                'payment_channel' => $this->record->payment_channel,
                'transaction_number' => $this->record->voucher_number,
                'transaction_date' => $this->record->voucher_date,
                'transaction_type' => $this->resolveTransactionType(),
                'party_type' => $this->record->party_type,
                'party_id' => $this->record->customer_id ?? $this->record->supplier_id,
                'party_name' => $partyName,
                'reference_type' => PaymentVoucher::class,
                'reference_id' => $this->record->id,
                'reference_number' => $this->record->voucher_number,
                'amount' => $this->record->amount,
                'description' => $this->record->description,
            ]);

            $this->record->update([
                'party_name' => $partyName,
                'branch_id' => $transaction->branch_id,
                'treasury_transaction_id' => $transaction->id,
                'status' => PaymentVoucher::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    private function resolvePartyName(): string
    {
        return match ($this->record->voucher_type) {
            PaymentVoucher::TYPE_SUPPLIER_PAYMENT => Supplier::find($this->record->supplier_id)?->name ?? '-',
            PaymentVoucher::TYPE_CUSTOMER_REFUND => Customer::find($this->record->customer_id)?->name ?? '-',
            PaymentVoucher::TYPE_GENERAL_EXPENSE => $this->record->category?->name ?? '-',
            default => $this->record->party_name ?? '-',
        };
    }

    private function resolveTransactionType(): string
    {
        return match ($this->record->voucher_type) {
            PaymentVoucher::TYPE_GENERAL_EXPENSE => TreasuryTransaction::TYPE_EXPENSE,
            default => TreasuryTransaction::TYPE_SUPPLIER_PAYMENT,
        };
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}