<?php

namespace App\Http\Controllers;

use App\Services\Reports\PartyBalanceReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierBalancePrintController extends Controller
{
    public function show(Request $request, PartyBalanceReportService $partyBalanceReportService): View
    {
        $report = $partyBalanceReportService->supplierBalances(
            fromDate: $request->query('from_date'),
            toDate: $request->query('to_date')
        );

        return view('prints.party-balance-report-print', [
            'report' => $report,
            'fromDate' => $report['from_date'],
            'toDate' => $report['to_date'],
            'partyLabel' => 'المورد',
            'partyPluralLabel' => 'الموردين',
            'title' => 'تقرير أرصدة الموردين',
        ]);
    }
}