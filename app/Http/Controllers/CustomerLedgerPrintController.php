<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\Finance\PartyLedgerService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerLedgerPrintController extends Controller
{
    public function show(Request $request, PartyLedgerService $ledgerService): View
    {
        $customerId = $request->integer('customer_id');

        if (! $customerId) {
            $customerId = Customer::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->value('id');
        }

        abort_unless($customerId, 404, 'لا يوجد عملاء لعرض كشف الحساب.');

        $ledger = $ledgerService->customerLedger(
            customerId: $customerId,
            fromDate: $request->query('from_date'),
            toDate: $request->query('to_date')
        );

        return view('prints.customer-ledger-print', [
            'ledger' => $ledger,
        ]);
    }
}