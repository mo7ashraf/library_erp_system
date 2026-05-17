<?php

namespace App\Http\Controllers;

use App\Models\StockCountDocument;
use Illuminate\View\View;

class StockCountReceiptController extends Controller
{
    public function show(StockCountDocument $stockCountDocument): View
    {
        $stockCountDocument->load([
            'warehouse.branch',
            'branch',
            'user',
            'items.item',
            'items.unit',
        ]);

        return view('prints.stock-count-receipt', [
            'document' => $stockCountDocument,
        ]);
    }
}