<?php

namespace App\Filament\Resources\ReceiptVouchers\Pages;

use App\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource;
use App\Models\Customer;
use App\Models\ReceiptVoucher;
use App\Models\Supplier;
use App\Models\TreasuryTransaction;
use App\Services\Finance\TreasuryService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = ReceiptVoucher::STATUS_DRAFT;

        match ($data['voucher_type'] ?? ReceiptVoucher::TYPE_CUSTOMER_COLLECTION) {
            ReceiptVoucher::TYPE_CUSTOMER_COLLECTION => $data['party_type'] = ReceiptVoucher::PARTY_CUSTOMER,
            ReceiptVoucher::TYPE_SUPPLIER_REFUND => $data['party_type'] = ReceiptVoucher::PARTY_SUPPLIER,
            default => $data['party_type'] = ReceiptVoucher::PARTY_OTHER,
        };

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            if ($this->record->status === ReceiptVoucher::STATUS_POSTED) {
                return;
            }

            $this->record->load(['category']);

            $partyName = $this->resolvePartyName();

            $transaction = app(TreasuryService::class)->receive([
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
                'reference_type' => ReceiptVoucher::class,
                'reference_id' => $this->record->id,
                'reference_number' => $this->record->voucher_number,
                'amount' => $this->record->amount,
                'description' => $this->record->description,
            ]);

            $this->record->update([
                'party_name' => $partyName,
                'branch_id' => $transaction->branch_id,
                'treasury_transaction_id' => $transaction->id,
                'status' => ReceiptVoucher::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    private function resolvePartyName(): string
    {
        return match ($this->record->voucher_type) {
            ReceiptVoucher::TYPE_CUSTOMER_COLLECTION => Customer::find($this->record->customer_id)?->name ?? '-',
            ReceiptVoucher::TYPE_SUPPLIER_REFUND => Supplier::find($this->record->supplier_id)?->name ?? '-',
            ReceiptVoucher::TYPE_GENERAL_INCOME => $this->record->category?->name ?? '-',
            default => $this->record->party_name ?? '-',
        };
    }

    private function resolveTransactionType(): string
    {
        return match ($this->record->voucher_type) {
            ReceiptVoucher::TYPE_GENERAL_INCOME => TreasuryTransaction::TYPE_INCOME,
            default => TreasuryTransaction::TYPE_CUSTOMER_RECEIPT,
        };
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}