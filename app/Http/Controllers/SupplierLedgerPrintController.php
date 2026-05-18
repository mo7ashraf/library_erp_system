<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\Finance\PartyLedgerService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierLedgerPrintController extends Controller
{
    public function show(Request $request, PartyLedgerService $ledgerService): View
    {
        $supplierId = $request->integer('supplier_id');

        if (! $supplierId) {
            $supplierId = Supplier::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->value('id');
        }

        abort_unless($supplierId, 404, 'لا يوجد موردين لعرض كشف الحساب.');

        $ledger = $ledgerService->supplierLedger(
            supplierId: $supplierId,
            fromDate: $request->query('from_date'),
            toDate: $request->query('to_date')
        );

        return view('prints.supplier-ledger-print', [
            'ledger' => $ledger,
        ]);
    }
}