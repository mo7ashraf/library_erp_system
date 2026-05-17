<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use Illuminate\View\View;

class SalesReturnReceiptController extends Controller
{
    public function show(SalesReturn $salesReturn): View
    {
        $salesReturn->load([
            'salesInvoice',
            'customer',
            'warehouse.branch',
            'branch',
            'user',
            'items.item',
            'items.unit',
        ]);

        return view('prints.sales-return-receipt', [
            'return' => $salesReturn,
        ]);
    }
}