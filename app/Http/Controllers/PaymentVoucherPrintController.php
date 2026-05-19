<?php

namespace App\Http\Controllers;

use App\Models\PaymentVoucher;
use Illuminate\View\View;

class PaymentVoucherPrintController extends Controller
{
    public function show(PaymentVoucher $paymentVoucher): View
    {
        $paymentVoucher->load([
            'branch',
            'user',
            'cashbox',
            'bankAccount',
            'customer',
            'supplier',
            'treasuryTransaction',
            'category',
        ]);

        return view('prints.payment-voucher-receipt', [
            'voucher' => $paymentVoucher,
        ]);
    }
}