<?php

namespace App\Filament\Resources\PurchaseInvoices\Pages;

use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use App\Models\Cashbox;
use App\Models\PaymentVoucher;
use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\TreasuryTransaction;
use App\Models\Warehouse;
use App\Services\Finance\TreasuryService;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePurchaseInvoice extends CreateRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    private ?int $selectedCashboxId = null;

    private float $selectedPaidAmount = 0;

    protected function beforeCreate(): void
    {
        $paymentType = (string) ($this->data['payment_type'] ?? PurchaseInvoice::PAYMENT_CASH);
        $cashboxId = (int) ($this->data['cashbox_id'] ?? 0);
        $paidAmount = (float) ($this->data['paid_amount'] ?? 0);
        $lines = $this->data['items'] ?? [];

        if (count($lines) === 0) {
            throw ValidationException::withMessages([
                'data.items' => 'يجب إضافة صنف واحد على الأقل.',
            ]);
        }

        foreach ($lines as $line) {
            $itemId = (int) ($line['item_id'] ?? 0);
            $quantity = (float) ($line['quantity'] ?? 0);
            $unitPrice = (float) ($line['unit_price'] ?? 0);

            if ($itemId <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'يجب اختيار الصنف.',
                ]);
            }

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'كمية الشراء يجب أن تكون أكبر من صفر.',
                ]);
            }

            if ($unitPrice <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'سعر الشراء يجب أن يكون أكبر من صفر.',
                ]);
            }
        }

        if ($paymentType !== PurchaseInvoice::PAYMENT_CREDIT && $cashboxId <= 0) {
            throw ValidationException::withMessages([
                'data.cashbox_id' => 'يجب اختيار الخزينة عند الدفع الكامل أو الجزئي.',
            ]);
        }

        if ($paymentType === PurchaseInvoice::PAYMENT_PARTIAL && $paidAmount <= 0) {
            throw ValidationException::withMessages([
                'data.paid_amount' => 'في الدفع الجزئي يجب إدخال مبلغ مدفوع أكبر من صفر.',
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $warehouse = Warehouse::find($data['warehouse_id']);

        $this->selectedCashboxId = isset($data['cashbox_id']) ? (int) $data['cashbox_id'] : null;
        $this->selectedPaidAmount = isset($data['paid_amount']) ? (float) $data['paid_amount'] : 0;

        unset($data['cashbox_id'], $data['paid_amount']);

        $data['branch_id'] = $warehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = PurchaseInvoice::STATUS_DRAFT;
        $data['subtotal'] = 0;
        $data['grand_total'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['warehouse', 'items']);

            if ($this->record->status === PurchaseInvoice::STATUS_POSTED) {
                return;
            }

            $this->record->recalculateTotals();
            $this->record->refresh();

            $this->validatePaidAmountAgainstGrandTotal($this->record);

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $stockService->increase([
                    'warehouse_id' => $this->record->warehouse_id,
                    'item_id' => $line->item_id,
                    'branch_id' => $this->record->branch_id,
                    'user_id' => auth()->id(),
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->net_unit_price,
                    'movement_type' => StockMovement::TYPE_PURCHASE,
                    'reference_type' => PurchaseInvoice::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->invoice_number,
                    'movement_date' => $this->record->invoice_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->update([
                'status' => PurchaseInvoice::STATUS_POSTED,
                'posted_at' => now(),
            ]);

            $paidAmount = $this->resolvedPaidAmount($this->record);

            if ($paidAmount > 0) {
                $this->createPaymentVoucherForInvoice($this->record, $paidAmount);
            }
        });
    }

    private function validatePaidAmountAgainstGrandTotal(PurchaseInvoice $invoice): void
    {
        if ($invoice->payment_type !== PurchaseInvoice::PAYMENT_PARTIAL) {
            return;
        }

        $grandTotal = (float) $invoice->grand_total;

        if ($this->selectedPaidAmount <= 0 || $this->selectedPaidAmount >= $grandTotal) {
            throw ValidationException::withMessages([
                'data.paid_amount' => 'في الدفع الجزئي يجب أن يكون المبلغ المدفوع أكبر من صفر وأقل من إجمالي الفاتورة.',
            ]);
        }
    }

    private function resolvedPaidAmount(PurchaseInvoice $invoice): float
    {
        return match ($invoice->payment_type) {
            PurchaseInvoice::PAYMENT_CREDIT => 0,
            PurchaseInvoice::PAYMENT_PARTIAL => (float) $this->selectedPaidAmount,
            default => (float) $invoice->grand_total,
        };
    }

    private function createPaymentVoucherForInvoice(PurchaseInvoice $invoice, float $paidAmount): void
    {
        if (! $this->selectedCashboxId) {
            throw ValidationException::withMessages([
                'data.cashbox_id' => 'الخزينة مطلوبة لإنشاء سند صرف تلقائي.',
            ]);
        }

        $supplier = Supplier::find($invoice->supplier_id);
        $cashbox = Cashbox::findOrFail($this->selectedCashboxId);

        if ((float) $cashbox->current_balance < $paidAmount) {
            throw ValidationException::withMessages([
                'data.cashbox_id' => 'رصيد الخزينة غير كافٍ لإتمام دفع فاتورة الشراء. الرصيد الحالي: '
                    . number_format((float) $cashbox->current_balance, 2),
            ]);
        }

        $voucher = PaymentVoucher::create([
            'voucher_number' => 'PAY-PUR-' . $invoice->id . '-' . now()->format('His'),
            'voucher_date' => $invoice->invoice_date,
            'voucher_type' => PaymentVoucher::TYPE_SUPPLIER_PAYMENT,
            'party_type' => PaymentVoucher::PARTY_SUPPLIER,
            'customer_id' => null,
            'supplier_id' => $invoice->supplier_id,
            'finance_category_id' => null,
            'party_name' => $supplier?->name ?? '-',
            'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
            'cashbox_id' => $this->selectedCashboxId,
            'bank_account_id' => null,
            'amount' => $paidAmount,
            'description' => 'صرف مقابل فاتورة شراء رقم ' . $invoice->invoice_number,
            'notes' => null,
            'branch_id' => $cashbox->branch_id,
            'user_id' => auth()->id(),
            'status' => PaymentVoucher::STATUS_DRAFT,
        ]);

        $transaction = app(TreasuryService::class)->pay([
            'branch_id' => $cashbox->branch_id,
            'user_id' => auth()->id(),
            'cashbox_id' => $this->selectedCashboxId,
            'bank_account_id' => null,
            'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
            'transaction_number' => $voucher->voucher_number,
            'transaction_date' => $voucher->voucher_date,
            'transaction_type' => TreasuryTransaction::TYPE_SUPPLIER_PAYMENT,
            'party_type' => PaymentVoucher::PARTY_SUPPLIER,
            'party_id' => $invoice->supplier_id,
            'party_name' => $supplier?->name ?? '-',
            'reference_type' => PaymentVoucher::class,
            'reference_id' => $voucher->id,
            'reference_number' => $voucher->voucher_number,
            'amount' => $paidAmount,
            'description' => $voucher->description,
        ]);

        $voucher->update([
            'treasury_transaction_id' => $transaction->id,
            'status' => PaymentVoucher::STATUS_POSTED,
            'posted_at' => now(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}