<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المبيعات</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
            background: #e5e7eb;
            font-size: 12.5px;
        }

        body {
            padding: 20px;
        }

        .print-actions {
            width: 210mm;
            max-width: 100%;
            margin: 0 auto 12px;
            display: flex;
            gap: 8px;
        }

        .btn {
            border: none;
            background: #111827;
            color: white;
            padding: 9px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-family: "Cairo", sans-serif;
            font-weight: 800;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 14mm;
            border: 1px solid #d1d5db;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header h1 {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
        }

        .header .subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-weight: 700;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .box {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px;
        }

        .box-title {
            margin: 0 0 8px;
            font-size: 14px;
            font-weight: 900;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
        }

        .line {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin: 5px 0;
        }

        .label {
            color: #6b7280;
            font-weight: 700;
        }

        .value {
            font-weight: 900;
            text-align: left;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .summary-card {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px;
            background: #f9fafb;
        }

        .summary-label {
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
        }

        .summary-value {
            margin-top: 5px;
            font-size: 15px;
            font-weight: 900;
        }

        .positive {
            color: #15803d;
            font-weight: 900;
        }

        .negative {
            color: #b91c1c;
            font-weight: 900;
        }

        .neutral {
            color: #374151;
            font-weight: 900;
        }

        .section-title {
            margin: 18px 0 8px;
            font-size: 16px;
            font-weight: 900;
            border-right: 5px solid #111827;
            padding-right: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #cfd6df;
            padding: 7px;
            vertical-align: top;
            line-height: 1.7;
        }

        th {
            background: #f3f4f6;
            color: #111827;
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        td {
            color: #111827;
            font-weight: 600;
        }

        .text-left {
            text-align: left;
        }

        .badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .footer-total td {
            background: #111827;
            color: white;
            font-weight: 900;
            border-color: #111827;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 36px;
            page-break-inside: avoid;
        }

        .signature {
            width: 30%;
            border-top: 1px solid #111827;
            text-align: center;
            padding-top: 8px;
            font-weight: 800;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            .page {
                width: 100%;
                min-height: auto;
                border: none;
                padding: 0;
                margin: 0;
            }

            .box,
            .summary-card,
            table,
            .signatures {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

@php
    $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    $totals = $report['totals'] ?? [];
@endphp

<div class="print-actions">
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="page">
    <div class="header">
        <h1>تقرير المبيعات</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="info-grid">
        <div class="box">
            <h3 class="box-title">بيانات التقرير</h3>

            <div class="line">
                <span class="label">نوع التقرير</span>
                <span class="value">تقرير ملخص المبيعات</span>
            </div>

            <div class="line">
                <span class="label">تاريخ الطباعة</span>
                <span class="value">{{ now()->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="box">
            <h3 class="box-title">الفترة</h3>

            <div class="line">
                <span class="label">من تاريخ</span>
                <span class="value">{{ $fromDate ?? 'بداية النظام' }}</span>
            </div>

            <div class="line">
                <span class="label">إلى تاريخ</span>
                <span class="value">{{ $toDate ?? 'حتى الآن' }}</span>
            </div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">إجمالي المبيعات</div>
            <div class="summary-value positive">{{ $money($totals['grand_total'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">عدد الفواتير</div>
            <div class="summary-value neutral">{{ $totals['invoices_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">متوسط قيمة الفاتورة</div>
            <div class="summary-value neutral">{{ $money($totals['average_invoice_value'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إجمالي الخصومات</div>
            <div class="summary-value negative">{{ $money($totals['discount_amount'] ?? 0) }}</div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">إجمالي قبل الخصم</div>
            <div class="summary-value neutral">{{ $money($totals['subtotal'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">خدمة</div>
            <div class="summary-value neutral">{{ $money($totals['service_amount'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">عمولات</div>
            <div class="summary-value neutral">{{ $money($totals['commission_amount'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">مبيعات نقدية</div>
            <div class="summary-value positive">{{ $money($totals['cash_total'] ?? 0) }}</div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">مبيعات آجلة</div>
            <div class="summary-value neutral">{{ $money($totals['credit_total'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">مبيعات جزئية</div>
            <div class="summary-value neutral">{{ $money($totals['partial_total'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">صافي بعد الخصم</div>
            <div class="summary-value positive">{{ $money($totals['grand_total'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">الفترة</div>
            <div class="summary-value neutral" style="font-size: 12px;">
                {{ $fromDate ?? '-' }} → {{ $toDate ?? '-' }}
            </div>
        </div>
    </div>

    <h2 class="section-title">المبيعات حسب نوع الدفع</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع الدفع</th>
            <th class="text-left">عدد الفواتير</th>
            <th class="text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['sales_by_payment_type'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['payment_type_label'] }}</span></td>
                <td class="text-left">{{ $row['invoices_count'] }}</td>
                <td class="text-left positive">{{ $money($row['total_sales']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">المبيعات حسب نوع السعر</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع السعر</th>
            <th class="text-left">عدد الفواتير</th>
            <th class="text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['sales_by_price_type'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['price_type_label'] }}</span></td>
                <td class="text-left">{{ $row['invoices_count'] }}</td>
                <td class="text-left positive">{{ $money($row['total_sales']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">أفضل العملاء حسب المبيعات</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>العميل</th>
            <th class="text-left">عدد الفواتير</th>
            <th class="text-left">إجمالي المبيعات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_customers'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td class="text-left">{{ $row['invoices_count'] }}</td>
                <td class="text-left positive">{{ $money($row['total_sales']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات عملاء في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">آخر فواتير المبيعات</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>التاريخ</th>
            <th>رقم الفاتورة</th>
            <th>العميل</th>
            <th>نوع الدفع</th>
            <th>نوع السعر</th>
            <th class="text-left">قبل الخصم</th>
            <th class="text-left">الخصم</th>
            <th class="text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['latest_invoices'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['date'] }}</td>
                <td><span class="badge">{{ $row['number'] }}</span></td>
                <td>{{ $row['customer'] }}</td>
                <td>{{ $row['payment_type'] }}</td>
                <td>{{ $row['price_type'] }}</td>
                <td class="text-left">{{ $money($row['subtotal']) }}</td>
                <td class="text-left negative">{{ $money($row['discount_amount']) }}</td>
                <td class="text-left positive">{{ $money($row['grand_total']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد فواتير مبيعات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature">المحاسب</div>
        <div class="signature">المراجع</div>
        <div class="signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>