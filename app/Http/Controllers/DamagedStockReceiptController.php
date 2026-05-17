<?php

namespace App\Http\Controllers;

use App\Models\DamagedStockDocument;
use Illuminate\View\View;

class DamagedStockReceiptController extends Controller
{
    public function show(DamagedStockDocument $damagedStockDocument): View
    {
        $damagedStockDocument->load([
            'warehouse.branch',
            'branch',
            'user',
            'items.item',
            'items.unit',
        ]);

        return view('prints.damaged-stock-receipt', [
            'document' => $damagedStockDocument,
        ]);
    }
}