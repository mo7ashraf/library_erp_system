<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use Illuminate\View\View;

class SalesInvoiceReceiptController extends Controller
{
    public function show(SalesInvoice $salesInvoice): View
    {
        $salesInvoice->load([
            'customer',
            'warehouse.branch',
            'branch',
            'user',
            'items.item',
            'items.unit',
        ]);

        return view('prints.sales-invoice-receipt', [
            'invoice' => $salesInvoice,
        ]);
    }
}