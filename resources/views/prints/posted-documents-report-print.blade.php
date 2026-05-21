<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مراجعة المستندات</title>


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

        .compact-table .col-type {
            width: 95px;
        }

        .compact-table .col-number {
            width: 85px;
        }

        .compact-table .col-party {
            width: 135px;
        }

        .compact-table .col-branch {
            width: 80px;
        }

        .compact-table .col-user {
            width: 80px;
        }

        .compact-table .col-status {
            width: 58px;
        }

        .compact-table .col-posted {
            width: 92px;
        }

        .compact-table .col-qty {
            width: 65px;
        }

        .compact-table .col-money {
            width: 78px;
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

    $statusLabel = function (?string $state): string {
        return match ($state) {
            'posted' => 'مرحّل',
            'draft' => 'مسودة',
            'all' => 'الكل',
            default => $state ?: '-',
        };
    };

    $statusClass = function (?string $state): string {
        return match ($state) {
            'posted' => 'positive',
            'draft' => 'warning',
            default => 'neutral',
        };
    };
@endphp

<div class="print-actions">
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="page">
    <div class="header">
        <h1>تقرير مراجعة المستندات</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="info-grid">
        <div class="box">
            <h3 class="box-title">بيانات التقرير</h3>

            <div class="line">
                <span class="label">نوع التقرير</span>
                <span class="value">مراجعة المستندات التشغيلية</span>
            </div>

            <div class="line">
                <span class="label">تاريخ الطباعة</span>
                <span class="value">{{ now()->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="box">
            <h3 class="box-title">الفترة والحالة</h3>

            <div class="line">
                <span class="label">من تاريخ</span>
                <span class="value">{{ $fromDate ?? 'بداية النظام' }}</span>
            </div>

            <div class="line">
                <span class="label">إلى تاريخ</span>
                <span class="value">{{ $toDate ?? 'حتى الآن' }}</span>
            </div>

            <div class="line">
                <span class="label">الحالة</span>
                <span class="value">{{ $statusLabel($status ?? 'all') }}</span>
            </div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">عدد المستندات</div>
            <div class="summary-value neutral">{{ $totals['documents_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">مستندات مرحلة</div>
            <div class="summary-value positive">{{ $totals['posted_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">مستندات مسودة</div>
            <div class="summary-value warning">{{ $totals['draft_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إجمالي القيم</div>
            <div class="summary-value neutral">{{ $money($totals['total_amount'] ?? 0) }}</div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">إجمالي الكميات</div>
            <div class="summary-value neutral">{{ $number($totals['total_quantity'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">من تاريخ</div>
            <div class="summary-value neutral" style="font-size: 12px;">{{ $fromDate ?? '-' }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إلى تاريخ</div>
            <div class="summary-value neutral" style="font-size: 12px;">{{ $toDate ?? '-' }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">فلتر الحالة</div>
            <div class="summary-value {{ $statusClass($status ?? 'all') }}">
                {{ $statusLabel($status ?? 'all') }}
            </div>
        </div>
    </div>

    <h2 class="section-title">ملخص حسب نوع المستند</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع المستند</th>
            <th class="text-left">العدد</th>
            <th class="text-left">مرحّل</th>
            <th class="text-left">مسودة</th>
            <th class="text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['by_document_type'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['document_type_label'] }}</td>
                <td class="text-left">{{ $row['documents_count'] }}</td>
                <td class="text-left positive">{{ $row['posted_count'] }}</td>
                <td class="text-left warning">{{ $row['draft_count'] }}</td>
                <td class="text-left">{{ $money($row['total_amount']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">ملخص حسب الحالة</h2>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الحالة</th>
            <th class="text-left">العدد</th>
            <th class="text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['by_status'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>
                    <span class="{{ $statusClass($row['status'] ?? null) }}">
                        {{ $row['status_label'] }}
                    </span>
                </td>
                <td class="text-left">{{ $row['documents_count'] }}</td>
                <td class="text-left">{{ $money($row['total_amount']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">تفاصيل المستندات</h2>

    <table class="compact-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-date">التاريخ</th>
            <th class="col-type">نوع المستند</th>
            <th class="col-number">رقم المستند</th>
            <th class="col-party">الطرف / المخزن</th>
            <th class="col-branch">الفرع</th>
            <th class="col-user">المستخدم</th>
            <th class="col-status">الحالة</th>
            <th class="col-posted">تاريخ الترحيل</th>
            <th class="col-qty text-left">الكمية</th>
            <th class="col-money text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['rows'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['document_date'] }}</td>
                <td><span class="badge">{{ $row['document_type_label'] }}</span></td>
                <td>{{ $row['document_number'] }}</td>
                <td>{{ $row['party'] }}</td>
                <td>{{ $row['branch'] }}</td>
                <td>{{ $row['user'] }}</td>
                <td>
                    <span class="{{ $statusClass($row['status'] ?? null) }}">
                        {{ $row['status_label'] }}
                    </span>
                </td>
                <td>{{ $row['posted_at'] }}</td>
                <td class="text-left">
                    {{ $row['quantity'] === null ? '-' : $number($row['quantity']) }}
                </td>
                <td class="text-left">{{ $money($row['amount']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد مستندات خلال الفترة المحددة.
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