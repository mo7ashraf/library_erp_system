<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

DB::transaction(function () {
    $invoice = App\Models\PurchaseInvoice::query()
        ->with(['items'])
        ->where('invoice_number', 'PUR-20260523-234235')
        ->firstOrFail();

    echo "Fixing invoice: {$invoice->invoice_number}" . PHP_EOL;

    foreach ($invoice->items as $line) {
        $line->save();
    }

    $invoice->recalculateTotals();
    $invoice->refresh();

    foreach ($invoice->items as $line) {
        $movement = App\Models\StockMovement::query()
            ->where('reference_type', App\Models\PurchaseInvoice::class)
            ->where('reference_id', $invoice->id)
            ->where('movement_type', App\Models\StockMovement::TYPE_PURCHASE)
            ->where('warehouse_id', $invoice->warehouse_id)
            ->where('item_id', $line->item_id)
            ->first();

        if (! $movement) {
            echo "No purchase movement found for item {$line->item_id}" . PHP_EOL;
            continue;
        }

        $oldQuantity = (float) $movement->quantity;
        $newQuantity = (float) $line->quantity;
        $quantityDelta = $oldQuantity - $newQuantity;

        $oldTotalCost = (float) $movement->total_cost;
        $newUnitCost = (float) $line->net_unit_price;
        $newTotalCost = $newQuantity * $newUnitCost;
        $costDelta = $oldTotalCost - $newTotalCost;

        $movement->update([
            'quantity' => $newQuantity,
            'unit_cost' => $newUnitCost,
            'total_cost' => $newTotalCost,
            'balance_after' => max(0, (float) $movement->balance_after - $quantityDelta),
        ]);

        App\Models\StockMovement::query()
            ->where('warehouse_id', $movement->warehouse_id)
            ->where('item_id', $movement->item_id)
            ->where('id', '>', $movement->id)
            ->orderBy('id')
            ->get()
            ->each(function ($laterMovement) use ($quantityDelta) {
                $laterMovement->update([
                    'balance_after' => max(0, (float) $laterMovement->balance_after - $quantityDelta),
                ]);
            });

        $balance = App\Models\WarehouseItemBalance::query()
            ->where('warehouse_id', $movement->warehouse_id)
            ->where('item_id', $movement->item_id)
            ->first();

        if ($balance) {
            $newBalanceQuantity = max(0, (float) $balance->quantity - $quantityDelta);
            $newBalanceTotalCost = max(0, (float) $balance->total_cost - $costDelta);

            $balance->update([
                'quantity' => $newBalanceQuantity,
                'total_cost' => $newBalanceTotalCost,
                'average_cost' => $newBalanceQuantity > 0 ? $newBalanceTotalCost / $newBalanceQuantity : 0,
            ]);
        }

        echo "Updated stock movement for item {$line->item_id}: {$oldQuantity} -> {$newQuantity}" . PHP_EOL;
    }

    $paymentVoucher = App\Models\PaymentVoucher::query()
        ->where('description', 'like', '%' . $invoice->invoice_number . '%')
        ->where('party_type', App\Models\PaymentVoucher::PARTY_SUPPLIER)
        ->where('supplier_id', $invoice->supplier_id)
        ->latest('id')
        ->first();

    if ($paymentVoucher) {
        $oldAmount = (float) $paymentVoucher->amount;
        $newAmount = (float) $invoice->grand_total;
        $amountDelta = $oldAmount - $newAmount;

        $paymentVoucher->update([
            'amount' => $newAmount,
        ]);

        $treasuryTransaction = $paymentVoucher->treasuryTransaction;

        if ($treasuryTransaction) {
            $treasuryTransaction->update([
                'amount' => $newAmount,
            ]);
        }

        if ($paymentVoucher->cashbox && $amountDelta > 0) {
            $paymentVoucher->cashbox->update([
                'current_balance' => (float) $paymentVoucher->cashbox->current_balance + $amountDelta,
            ]);
        }

        echo "Updated payment voucher {$paymentVoucher->voucher_number}: {$oldAmount} -> {$newAmount}" . PHP_EOL;
    }

    echo "Invoice totals now: subtotal={$invoice->subtotal}, grand_total={$invoice->grand_total}" . PHP_EOL;
});

echo "Done." . PHP_EOL;