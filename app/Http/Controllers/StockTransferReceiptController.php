<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use Illuminate\View\View;

class StockTransferReceiptController extends Controller
{
    public function show(StockTransfer $stockTransfer): View
    {
        $stockTransfer->load([
            'fromWarehouse',
            'toWarehouse',
            'fromBranch',
            'toBranch',
            'user',
            'items.item',
            'items.unit',
        ]);

        return view('prints.stock-transfer-receipt', [
            'transfer' => $stockTransfer,
        ]);
    }
}