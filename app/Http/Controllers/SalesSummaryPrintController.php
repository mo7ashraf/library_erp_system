<?php

namespace App\Http\Controllers;

use App\Services\Reports\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesSummaryPrintController extends Controller
{
    public function show(Request $request, SalesReportService $salesReportService): View
    {
        $report = $salesReportService->summary(
            fromDate: $request->query('from_date'),
            toDate: $request->query('to_date')
        );

        return view('prints.sales-summary-report-print', [
            'fromDate' => $report['from_date'],
            'toDate' => $report['to_date'],
            'report' => $report,
        ]);
    }
}