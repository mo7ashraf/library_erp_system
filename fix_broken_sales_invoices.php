<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$numbers = [
    'SAL-20260522-134206',
    'SAL-20260522-134207',
];

$invoices = App\Models\SalesInvoice::query()
    ->whereIn('invoice_number', $numbers)
    ->with('items')
    ->get();

foreach ($invoices as $invoice) {
    if ($invoice->status === App\Models\SalesInvoice::STATUS_DRAFT) {
        $invoice->items()->delete();
        $invoice->delete();

        echo 'Deleted broken draft invoice: ' . $invoice->invoice_number . PHP_EOL;
        continue;
    }

    $invoice->recalculateTotals();

    echo 'Recalculated invoice: ' . $invoice->invoice_number . PHP_EOL;
}

echo 'Done.' . PHP_EOL;