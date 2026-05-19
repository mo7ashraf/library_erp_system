<?php

namespace App\Http\Controllers;

use App\Services\Finance\FinancialReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialSummaryPrintController extends Controller
{
    public function show(Request $request, FinancialReportService $financialReportService): View
    {
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $report = $financialReportService->summary(
            fromDate: $fromDate,
            toDate: $toDate
        );

        return view('prints.financial-summary-report-print', [
            'fromDate' => $report['from_date'],
            'toDate' => $report['to_date'],
            'report' => $report,
        ]);
    }
}