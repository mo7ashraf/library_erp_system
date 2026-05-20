<?php

namespace App\Http\Controllers;

use App\Services\Reports\PostedDocumentsReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostedDocumentsPrintController extends Controller
{
    public function show(Request $request, PostedDocumentsReportService $postedDocumentsReportService): View
    {
        $report = $postedDocumentsReportService->summary(
            fromDate: $request->query('from_date'),
            toDate: $request->query('to_date'),
            status: $request->query('status') ?: 'all'
        );

        return view('prints.posted-documents-report-print', [
            'fromDate' => $report['from_date'],
            'toDate' => $report['to_date'],
            'status' => $report['status'],
            'report' => $report,
        ]);
    }
}