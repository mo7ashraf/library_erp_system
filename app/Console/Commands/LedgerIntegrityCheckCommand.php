<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\PaymentVoucher;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\ReceiptVoucher;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\Supplier;
use App\Services\Finance\PartyLedgerService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LedgerIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-ledgers';

    protected $description = 'Checks customer and supplier ledger balances against saved ERP documents.';

    private int $errors = 0;

    public function handle(): int
    {
        $this->info('Starting customer/supplier ledger integrity check...');
        $this->line('------------------------------------------');

        $this->checkCustomerLedgers();
        $this->checkSupplierLedgers();

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Ledger integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Ledger integrity check passed.');

        return self::SUCCESS;
    }

    private function checkCustomerLedgers(): void
    {
        $this->info('Checking customer ledgers...');

        Customer::query()
            ->orderBy('id')
            ->get()
            ->each(function (Customer $customer): void {
                $expected = $this->openingSignedBalance(
                    (float) $customer->opening_balance,
                    $customer->balance_type
                );

                $salesInvoices = (float) SalesInvoice::query()
                    ->where('customer_id', $customer->id)
                    ->where('status', SalesInvoice::STATUS_POSTED)
                    ->sum('grand_total');

                $salesReturns = (float) SalesReturn::query()
                    ->where('customer_id', $customer->id)
                    ->where('status', SalesReturn::STATUS_POSTED)
                    ->sum('grand_total');

                $receiptVouchers = (float) ReceiptVoucher::query()
                    ->where('party_type', ReceiptVoucher::PARTY_CUSTOMER)
                    ->where('customer_id', $customer->id)
                    ->where('status', ReceiptVoucher::STATUS_POSTED)
                    ->sum('amount');

                $paymentVouchers = (float) PaymentVoucher::query()
                    ->where('party_type', PaymentVoucher::PARTY_CUSTOMER)
                    ->where('customer_id', $customer->id)
                    ->where('status', PaymentVoucher::STATUS_POSTED)
                    ->sum('amount');

                /*
                 * Customer balance convention:
                 * Sales invoice     = debit  (+)
                 * Sales return      = credit (-)
                 * Receipt voucher   = credit (-)
                 * Payment to client = debit  (+)
                 */
                $expected += $salesInvoices;
                $expected -= $salesReturns;
                $expected -= $receiptVouchers;
                $expected += $paymentVouchers;

                $ledger = app(PartyLedgerService::class)->customerLedger($customer->id);

                $actual = (float) $ledger['closing_balance'];

                $this->assertClose(
                    $actual,
                    $expected,
                    "Customer ledger {$customer->code} - {$customer->name}"
                );
            });
    }

    private function checkSupplierLedgers(): void
    {
        $this->info('Checking supplier ledgers...');

        Supplier::query()
            ->orderBy('id')
            ->get()
            ->each(function (Supplier $supplier): void {
                $expected = $this->openingSignedBalance(
                    (float) $supplier->opening_balance,
                    $supplier->balance_type
                );

                $purchaseInvoices = (float) PurchaseInvoice::query()
                    ->where('supplier_id', $supplier->id)
                    ->where('status', PurchaseInvoice::STATUS_POSTED)
                    ->sum('grand_total');

                $purchaseReturns = (float) PurchaseReturn::query()
                    ->where('supplier_id', $supplier->id)
                    ->where('status', PurchaseReturn::STATUS_POSTED)
                    ->sum('grand_total');

                $paymentVouchers = (float) PaymentVoucher::query()
                    ->where('party_type', PaymentVoucher::PARTY_SUPPLIER)
                    ->where('supplier_id', $supplier->id)
                    ->where('status', PaymentVoucher::STATUS_POSTED)
                    ->sum('amount');

                $receiptVouchers = (float) ReceiptVoucher::query()
                    ->where('party_type', ReceiptVoucher::PARTY_SUPPLIER)
                    ->where('supplier_id', $supplier->id)
                    ->where('status', ReceiptVoucher::STATUS_POSTED)
                    ->sum('amount');

                /*
                 * Supplier balance convention:
                 * Purchase invoice       = credit (-)
                 * Purchase return        = debit  (+)
                 * Payment to supplier    = debit  (+)
                 * Receipt from supplier  = debit  (+)
                 */
                $expected -= $purchaseInvoices;
                $expected += $purchaseReturns;
                $expected += $paymentVouchers;
                $expected += $receiptVouchers;

                $ledger = app(PartyLedgerService::class)->supplierLedger($supplier->id);

                $actual = (float) $ledger['closing_balance'];

                $this->assertClose(
                    $actual,
                    $expected,
                    "Supplier ledger {$supplier->code} - {$supplier->name}"
                );
            });
    }

    private function openingSignedBalance(float $amount, ?string $balanceType): float
    {
        $normalized = Str::lower(trim((string) $balanceType));

        if (in_array($normalized, ['credit', 'cr', 'creditor', 'دائن'], true)) {
            return -1 * abs($amount);
        }

        return abs($amount);
    }

    private function assertClose(float $actual, float $expected, string $label, float $tolerance = 0.01): void
    {
        if (abs($actual - $expected) > $tolerance) {
            $this->errors++;

            $this->error("✗ {$label}: expected {$expected}, actual {$actual}");

            return;
        }

        $this->line("✓ {$label}");
    }
}