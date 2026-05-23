<?php

namespace App\Filament\Resources\SalesInvoices\Pages;

use App\Filament\Resources\SalesInvoices\SalesInvoiceResource;
use App\Models\Cashbox;
use App\Models\Customer;
use App\Models\ReceiptVoucher;
use App\Models\SalesInvoice;
use App\Models\StockMovement;
use App\Models\TreasuryTransaction;
use App\Models\Warehouse;
use App\Models\WarehouseItemBalance;
use App\Services\Finance\TreasuryService;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSalesInvoice extends CreateRecord
{
    protected static string $resource = SalesInvoiceResource::class;

    private ?int $selectedCashboxId = null;

    private float $selectedPaidAmount = 0;

    protected function beforeCreate(): void
    {
        $warehouseId = (int) ($this->data['warehouse_id'] ?? 0);
        $paymentType = (string) ($this->data['payment_type'] ?? SalesInvoice::PAYMENT_CASH);
        $cashboxId = (int) ($this->data['cashbox_id'] ?? 0);
        $paidAmount = (float) ($this->data['paid_amount'] ?? 0);
        $lines = $this->data['items'] ?? [];

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
                    'data.items' => 'كمية البيع يجب أن تكون أكبر من صفر.',
                ]);
            }

            if ($unitPrice <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'لا يمكن إنشاء فاتورة بيع بسعر صفر. راجع سعر الصنف.',
                ]);
            }

            $availableQuantity = (float) WarehouseItemBalance::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->value('quantity');

            if ($availableQuantity <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'هذا الصنف غير متاح في المخزن المحدد.',
                ]);
            }

            if ($quantity > $availableQuantity) {
                throw ValidationException::withMessages([
                    'data.items' => 'كمية البيع لا يمكن أن تتجاوز المتاح في المخزن. المتاح حاليًا: '
                        . number_format($availableQuantity, 3),
                ]);
            }
        }

        if ($paymentType !== SalesInvoice::PAYMENT_CREDIT && $cashboxId <= 0) {
            throw ValidationException::withMessages([
                'data.cashbox_id' => 'يجب اختيار الخزينة عند الدفع الكامل أو الجزئي.',
            ]);
        }

        if ($paymentType === SalesInvoice::PAYMENT_PARTIAL && $paidAmount <= 0) {
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
        $data['status'] = SalesInvoice::STATUS_DRAFT;
        $data['subtotal'] = 0;
        $data['grand_total'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['warehouse', 'items']);

            if ($this->record->status === SalesInvoice::STATUS_POSTED) {
                return;
            }

            $this->record->recalculateTotals();
            $this->record->refresh();

            $this->validatePaidAmountAgainstGrandTotal($this->record);

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $averageCost = (float) WarehouseItemBalance::query()
                    ->where('warehouse_id', $this->record->warehouse_id)
                    ->where('item_id', $line->item_id)
                    ->value('average_cost');

                $stockService->decrease([
                    'warehouse_id' => $this->record->warehouse_id,
                    'item_id' => $line->item_id,
                    'branch_id' => $this->record->branch_id,
                    'user_id' => auth()->id(),
                    'quantity' => $line->quantity,
                    'unit_cost' => $averageCost,
                    'movement_type' => StockMovement::TYPE_SALE,
                    'reference_type' => SalesInvoice::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->invoice_number,
                    'movement_date' => $this->record->invoice_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->update([
                'status' => SalesInvoice::STATUS_POSTED,
                'posted_at' => now(),
            ]);

            $paidAmount = $this->resolvedPaidAmount($this->record);

            if ($paidAmount > 0) {
                $this->createReceiptVoucherForInvoice($this->record, $paidAmount);
            }
        });
    }

    private function validatePaidAmountAgainstGrandTotal(SalesInvoice $invoice): void
    {
        if ($invoice->payment_type !== SalesInvoice::PAYMENT_PARTIAL) {
            return;
        }

        $grandTotal = (float) $invoice->grand_total;

        if ($this->selectedPaidAmount <= 0 || $this->selectedPaidAmount >= $grandTotal) {
            throw ValidationException::withMessages([
                'data.paid_amount' => 'في الدفع الجزئي يجب أن يكون المبلغ المدفوع أكبر من صفر وأقل من إجمالي الفاتورة.',
            ]);
        }
    }

    private function resolvedPaidAmount(SalesInvoice $invoice): float
    {
        return match ($invoice->payment_type) {
            SalesInvoice::PAYMENT_CREDIT => 0,
            SalesInvoice::PAYMENT_PARTIAL => (float) $this->selectedPaidAmount,
            default => (float) $invoice->grand_total,
        };
    }

    private function createReceiptVoucherForInvoice(SalesInvoice $invoice, float $paidAmount): void
    {
        if (! $this->selectedCashboxId) {
            throw ValidationException::withMessages([
                'data.cashbox_id' => 'الخزينة مطلوبة لإنشاء سند قبض تلقائي.',
            ]);
        }

        $customer = Customer::find($invoice->customer_id);
        $cashbox = Cashbox::findOrFail($this->selectedCashboxId);

        $voucher = ReceiptVoucher::create([
            'voucher_number' => 'RCV-SALE-' . $invoice->id . '-' . now()->format('His'),
            'voucher_date' => $invoice->invoice_date,
            'voucher_type' => ReceiptVoucher::TYPE_CUSTOMER_COLLECTION,
            'party_type' => ReceiptVoucher::PARTY_CUSTOMER,
            'customer_id' => $invoice->customer_id,
            'supplier_id' => null,
            'finance_category_id' => null,
            'party_name' => $customer?->name ?? '-',
            'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
            'cashbox_id' => $this->selectedCashboxId,
            'bank_account_id' => null,
            'amount' => $paidAmount,
            'description' => 'تحصيل من فاتورة بيع رقم ' . $invoice->invoice_number,
            'notes' => null,
            'branch_id' => $cashbox->branch_id,
            'user_id' => auth()->id(),
            'status' => ReceiptVoucher::STATUS_DRAFT,
        ]);

        $transaction = app(TreasuryService::class)->receive([
            'branch_id' => $cashbox->branch_id,
            'user_id' => auth()->id(),
            'cashbox_id' => $this->selectedCashboxId,
            'bank_account_id' => null,
            'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
            'transaction_number' => $voucher->voucher_number,
            'transaction_date' => $voucher->voucher_date,
            'transaction_type' => TreasuryTransaction::TYPE_CUSTOMER_RECEIPT,
            'party_type' => ReceiptVoucher::PARTY_CUSTOMER,
            'party_id' => $invoice->customer_id,
            'party_name' => $customer?->name ?? '-',
            'reference_type' => ReceiptVoucher::class,
            'reference_id' => $voucher->id,
            'reference_number' => $voucher->voucher_number,
            'amount' => $paidAmount,
            'description' => $voucher->description,
        ]);

        $voucher->update([
            'treasury_transaction_id' => $transaction->id,
            'status' => ReceiptVoucher::STATUS_POSTED,
            'posted_at' => now(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}