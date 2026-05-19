<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الملخص المالي</title>

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

        .page-break {
            page-break-before: always;
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

    $totalInflow = (float) ($report['total_inflow'] ?? 0);
    $totalOutflow = (float) ($report['total_outflow'] ?? 0);
    $netMovement = $totalInflow - $totalOutflow;

    $valueClass = function (float $value): string {
        if ($value > 0) {
            return 'positive';
        }

        if ($value < 0) {
            return 'negative';
        }

        return 'neutral';
    };
@endphp

<div class="print-actions">
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="page">
    <div class="header">
        <h1>الملخص المالي</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="info-grid">
        <div class="box">
            <h3 class="box-title">بيانات التقرير</h3>

            <div class="line">
                <span class="label">نوع التقرير</span>
                <span class="value">ملخص مالي</span>
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
            <div class="summary-label">إجمالي الداخل</div>
            <div class="summary-value positive">{{ $money($totalInflow) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إجمالي الخارج</div>
            <div class="summary-value negative">{{ $money($totalOutflow) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">صافي الحركة</div>
            <div class="summary-value {{ $valueClass($netMovement) }}">
                {{ $money($netMovement) }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إجمالي الأرصدة الحالية</div>
            <div class="summary-value neutral">
                {{ $money(($report['cashbox_total_balance'] ?? 0) + ($report['bank_total_balance'] ?? 0)) }}
            </div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">داخل الخزائن</div>
            <div class="summary-value positive">{{ $money($report['cash_inflow'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">خارج الخزائن</div>
            <div class="summary-value negative">{{ $money($report['cash_outflow'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">داخل البنوك</div>
            <div class="summary-value positive">{{ $money($report['bank_inflow'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">خارج البنوك</div>
            <div class="summary-value negative">{{ $money($report['bank_outflow'] ?? 0) }}</div>
        </div>
    </div>

    <h2 class="section-title">ملخص أنواع الحركات</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع الحركة</th>
            <th>الاتجاه</th>
            <th class="text-left">عدد الحركات</th>
            <th class="text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['transaction_type_summary'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['transaction_type_label'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'positive' : 'negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="text-left">{{ $row['transactions_count'] }}</td>
                <td class="text-left {{ $row['direction'] === 'in' ? 'positive' : 'negative' }}">
                    {{ $money($row['total_amount']) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">أرصدة الخزائن</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الخزينة</th>
            <th>الفرع</th>
            <th class="text-left">داخل الفترة</th>
            <th class="text-left">خارج الفترة</th>
            <th class="text-left">الرصيد الحالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['cashboxes'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['branch_name'] }}</td>
                <td class="text-left positive">{{ $money($row['period_in']) }}</td>
                <td class="text-left negative">{{ $money($row['period_out']) }}</td>
                <td class="text-left neutral">{{ $money($row['current_balance']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد خزائن.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">أرصدة البنوك</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الحساب</th>
            <th>البنك</th>
            <th class="text-left">داخل الفترة</th>
            <th class="text-left">خارج الفترة</th>
            <th class="text-left">الرصيد الحالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['bank_accounts'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td>{{ $row['bank_name'] }}</td>
                <td class="text-left positive">{{ $money($row['period_in']) }}</td>
                <td class="text-left negative">{{ $money($row['period_out']) }}</td>
                <td class="text-left neutral">{{ $money($row['current_balance']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حسابات بنكية.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">آخر الحركات المالية</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>التاريخ</th>
            <th>رقم الحركة</th>
            <th>النوع</th>
            <th>الحساب</th>
            <th>الاتجاه</th>
            <th class="text-left">المبلغ</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['latest_transactions'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['transaction_date'] }}</td>
                <td>{{ $row['transaction_number'] }}</td>
                <td><span class="badge">{{ $row['transaction_type_label'] }}</span></td>
                <td>{{ $row['account_name'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'positive' : 'negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="text-left {{ $row['direction'] === 'in' ? 'positive' : 'negative' }}">
                    {{ $money($row['amount']) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات مالية في الفترة المحددة.
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