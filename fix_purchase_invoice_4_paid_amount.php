<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

DB::transaction(function () {
    $invoice = App\Models\PurchaseInvoice::query()
        ->where('id', 4)
        ->firstOrFail();

    $newPaidAmount = 100.00;

    $voucher = App\Models\PaymentVoucher::query()
        ->where('party_type', App\Models\PaymentVoucher::PARTY_SUPPLIER)
        ->where('supplier_id', $invoice->supplier_id)
        ->where('status', App\Models\PaymentVoucher::STATUS_POSTED)
        ->where('description', 'like', '%' . $invoice->invoice_number . '%')
        ->latest('id')
        ->first();

    if (! $voucher) {
        echo "No payment voucher found for invoice {$invoice->invoice_number}" . PHP_EOL;
        exit;
    }

    $oldAmount = (float) $voucher->amount;
    $differenceToReturnToCashbox = $oldAmount - $newPaidAmount;

    echo "Invoice: {$invoice->invoice_number}" . PHP_EOL;
    echo "Old paid amount: {$oldAmount}" . PHP_EOL;
    echo "New paid amount: {$newPaidAmount}" . PHP_EOL;

    $voucher->update([
        'amount' => $newPaidAmount,
    ]);

    if ($voucher->treasuryTransaction) {
        $voucher->treasuryTransaction->update([
            'amount' => $newPaidAmount,
        ]);
    }

    if ($voucher->cashbox && $differenceToReturnToCashbox > 0) {
        $voucher->cashbox->update([
            'current_balance' => (float) $voucher->cashbox->current_balance + $differenceToReturnToCashbox,
        ]);

        echo "Returned to cashbox: {$differenceToReturnToCashbox}" . PHP_EOL;
    }

    echo "Payment voucher updated: {$voucher->voucher_number}" . PHP_EOL;
});

echo "Done." . PHP_EOL;