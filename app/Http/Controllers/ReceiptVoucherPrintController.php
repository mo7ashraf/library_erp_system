<?php

namespace App\Http\Controllers;

use App\Models\ReceiptVoucher;
use Illuminate\View\View;

class ReceiptVoucherPrintController extends Controller
{
    public function show(ReceiptVoucher $receiptVoucher): View
    {
        $receiptVoucher->load([
            'branch',
            'user',
            'cashbox',
            'bankAccount',
            'customer',
            'supplier',
            'treasuryTransaction',
        ]);

        return view('prints.receipt-voucher-receipt', [
            'voucher' => $receiptVoucher,
        ]);
    }
}