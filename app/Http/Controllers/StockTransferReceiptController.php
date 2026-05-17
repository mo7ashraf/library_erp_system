<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use Illuminate\View\View;

class PurchaseReturnReceiptController extends Controller
{
    public function show(PurchaseReturn $purchaseReturn): View
    {
        $purchaseReturn->load([
            'purchaseInvoice',
            'supplier',
            'warehouse.branch',
            'branch',
            'user',
            'items.item',
            'items.unit',
        ]);

        return view('prints.purchase-return-receipt', [
            'return' => $purchaseReturn,
        ]);
    }
}