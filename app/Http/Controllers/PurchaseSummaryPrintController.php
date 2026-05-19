<?php

namespace App\Http\Controllers;

use App\Services\Reports\PurchaseReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseSummaryPrintController extends Controller
{
    public function show(Request $request, PurchaseReportService $purchaseReportService): View
    {
        $report = $purchaseReportService->summary(
            fromDate: $request->query('from_date'),
            toDate: $request->query('to_date')
        );

        return view('prints.purchase-summary-report-print', [
            'fromDate' => $report['from_date'],
            'toDate' => $report['to_date'],
            'report' => $report,
        ]);
    }
}