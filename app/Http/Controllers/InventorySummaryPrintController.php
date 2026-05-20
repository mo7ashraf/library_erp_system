<?php

namespace App\Http\Controllers;

use App\Services\Reports\InventoryReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventorySummaryPrintController extends Controller
{
    public function show(Request $request, InventoryReportService $inventoryReportService): View
    {
        $report = $inventoryReportService->summary(
            fromDate: $request->query('from_date'),
            toDate: $request->query('to_date')
        );

        return view('prints.inventory-summary-report-print', [
            'fromDate' => $report['from_date'],
            'toDate' => $report['to_date'],
            'report' => $report,
        ]);
    }
}