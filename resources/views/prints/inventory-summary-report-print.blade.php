<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المخزون</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
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
            font-size: 12px;
        }

        body {
            padding: 20px;
        }

        .print-actions {
            width: 297mm;
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
            width: 297mm;
            min-height: 210mm;
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 12mm;
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

        .warning {
            color: #c2410c;
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

        .compact-table {
            table-layout: auto;
            font-size: 11px;
        }

        .compact-table th,
        .compact-table td {
            padding: 5px 4px;
            line-height: 1.35;
            overflow-wrap: break-word;
        }

        .compact-table th {
            white-space: normal;
            text-align: center;
            vertical-align: middle;
        }

        .compact-table .col-small {
            width: 30px;
        }

        .compact-table .col-date {
            width: 62px;
        }

        .compact-table .col-ref {
            width: 80px;
        }

        .compact-table .col-type {
            width: 95px;
        }

        .compact-table .col-name {
            width: 135px;
        }

        .compact-table .col-warehouse {
            width: 90px;
        }

        .compact-table .col-direction {
            width: 52px;
        }

        .compact-table .col-number {
            width: 70px;
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
    $number = fn ($value) => number_format((float) $value, 3);
    $totals = $report['totals'] ?? [];
@endphp

<div class="print-actions">
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="page">
    <div class="header">
        <h1>تقرير المخزون</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="info-grid">
        <div class="box">
            <h3 class="box-title">بيانات التقرير</h3>

            <div class="line">
                <span class="label">نوع التقرير</span>
                <span class="value">تقرير ملخص المخزون</span>
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
            <div class="summary-label">قيمة المخزون الحالية</div>
            <div class="summary-value positive">{{ $money($totals['total_value'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إجمالي الكمية</div>
            <div class="summary-value neutral">{{ $number($totals['total_quantity'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">أصناف لها رصيد</div>
            <div class="summary-value neutral">{{ $totals['items_with_stock_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">مخازن بها أرصدة</div>
            <div class="summary-value neutral">{{ $totals['warehouses_count'] ?? 0 }}</div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">أصناف رصيدها صفر</div>
            <div class="summary-value negative">{{ $totals['zero_stock_items_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">أصناف تحت حد الطلب</div>
            <div class="summary-value warning">{{ $totals['low_stock_items_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">الفترة من</div>
            <div class="summary-value neutral" style="font-size: 12px;">{{ $fromDate ?? '-' }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">الفترة إلى</div>
            <div class="summary-value neutral" style="font-size: 12px;">{{ $toDate ?? '-' }}</div>
        </div>
    </div>

    <h2 class="section-title">الأرصدة حسب المخزن</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>المخزن</th>
            <th class="text-left">عدد الأصناف</th>
            <th class="text-left">إجمالي الكمية</th>
            <th class="text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['balances_by_warehouse'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['warehouse_name'] }}</td>
                <td class="text-left">{{ $row['items_count'] }}</td>
                <td class="text-left">{{ $number($row['total_quantity']) }}</td>
                <td class="text-left positive">{{ $money($row['total_value']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أرصدة مخزون.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">أعلى الأصناف قيمة</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الكود</th>
            <th>الصنف</th>
            <th class="text-left">الكمية</th>
            <th class="text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_value_items'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['item_code'] }}</span></td>
                <td>{{ $row['item_name'] }}</td>
                <td class="text-left">{{ $number($row['total_quantity']) }}</td>
                <td class="text-left positive">{{ $money($row['total_value']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أصناف لها قيمة مخزون.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">أصناف تحت حد الطلب</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الكود</th>
            <th>الصنف</th>
            <th class="text-left">الرصيد</th>
            <th class="text-left">حد الطلب</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['low_stock_items'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['item_code'] }}</span></td>
                <td>{{ $row['item_name'] }}</td>
                <td class="text-left warning">{{ $number($row['stock_quantity']) }}</td>
                <td class="text-left">{{ $number($row['threshold']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أصناف تحت حد الطلب.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">أصناف رصيدها صفر</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الكود</th>
            <th>الصنف</th>
            <th class="text-left">الرصيد</th>
            <th class="text-left">حد الطلب</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['zero_stock_items'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['item_code'] }}</span></td>
                <td>{{ $row['item_name'] }}</td>
                <td class="text-left negative">{{ $number($row['stock_quantity']) }}</td>
                <td class="text-left">{{ $number($row['reorder_level']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أصناف رصيدها صفر.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">ملخص حركة المخزون</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع الحركة</th>
            <th>الاتجاه</th>
            <th class="text-left">عدد الحركات</th>
            <th class="text-left">الكمية</th>
            <th class="text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['movement_summary'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['movement_type_label'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'positive' : 'negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="text-left">{{ $row['movements_count'] }}</td>
                <td class="text-left">{{ $number($row['total_quantity']) }}</td>
                <td class="text-left">{{ $money($row['total_cost']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات مخزون خلال الفترة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">آخر حركات المخزون</h2>

    <table class="compact-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-date">التاريخ</th>
            <th class="col-ref">المرجع</th>
            <th class="col-type">نوع الحركة</th>
            <th class="col-name">الصنف</th>
            <th class="col-warehouse">المخزن</th>
            <th class="col-direction">الاتجاه</th>
            <th class="col-number text-left">الكمية</th>
            <th class="col-number text-left">القيمة</th>
            <th class="col-number text-left">الرصيد بعد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['latest_movements'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['reference_number'] }}</td>
                <td><span class="badge">{{ $row['movement_type'] }}</span></td>
                <td>{{ $row['item'] }}</td>
                <td>{{ $row['warehouse'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'positive' : 'negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="text-left">{{ $number($row['quantity']) }}</td>
                <td class="text-left">{{ $money($row['total_cost']) }}</td>
                <td class="text-left">{{ $number($row['balance_after']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات مخزون خلال الفترة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature">أمين المخزن</div>
        <div class="signature">المراجع</div>
        <div class="signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>