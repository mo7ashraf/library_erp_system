<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'تقرير الأرصدة' }}</title>


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

        .compact-table .col-code {
            width: 70px;
        }

        .compact-table .col-name {
            width: 150px;
        }

        .compact-table .col-branch {
            width: 90px;
        }

        .compact-table .col-phone {
            width: 90px;
        }

        .compact-table .col-money {
            width: 85px;
        }

        .compact-table .col-balance {
            width: 95px;
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

    $balanceClass = function (?string $side): string {
        return match ($side) {
            'debit' => 'positive',
            'credit' => 'negative',
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
        <h1>{{ $title ?? ($report['title'] ?? 'تقرير الأرصدة') }}</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="info-grid">
        <div class="box">
            <h3 class="box-title">بيانات التقرير</h3>

            <div class="line">
                <span class="label">نوع التقرير</span>
                <span class="value">{{ $report['title'] ?? 'تقرير الأرصدة' }}</span>
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
            <div class="summary-label">عدد {{ $partyPluralLabel }}</div>
            <div class="summary-value neutral">{{ $totals['parties_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">أرصدة مدينة</div>
            <div class="summary-value positive">{{ $money($totals['closing_debit_total'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">أرصدة دائنة</div>
            <div class="summary-value negative">{{ $money($totals['closing_credit_total'] ?? 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">صافي الرصيد</div>
            <div class="summary-value {{ ($totals['net_balance'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                {{ $money(abs((float) ($totals['net_balance'] ?? 0))) }}
                {{ ($totals['net_balance'] ?? 0) >= 0 ? 'مدين' : 'دائن' }}
            </div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">عدد الأرصدة المدينة</div>
            <div class="summary-value positive">{{ $totals['debit_parties_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">عدد الأرصدة الدائنة</div>
            <div class="summary-value negative">{{ $totals['credit_parties_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">أرصدة صفرية</div>
            <div class="summary-value neutral">{{ $totals['zero_parties_count'] ?? 0 }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">حركة الفترة</div>
            <div class="summary-value neutral" style="font-size: 12px;">
                مدين: {{ $money($totals['period_debit_total'] ?? 0) }}
                <br>
                دائن: {{ $money($totals['period_credit_total'] ?? 0) }}
            </div>
        </div>
    </div>

    <h2 class="section-title">أعلى أرصدة مدينة</h2>

    <table>
        <thead>
        <tr>
            <th style="width:35px;text-align:center;">م</th>
            <th>الكود</th>
            <th>{{ $partyLabel }}</th>
            <th class="text-left">الرصيد</th>
            <th class="text-left">الحركات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_debit'] ?? [] as $row)
            <tr>
                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['code'] }}</span></td>
                <td>{{ $row['name'] }}</td>
                <td class="text-left positive">{{ $row['closing_balance_label'] }}</td>
                <td class="text-left">{{ $row['rows_count'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:20px;color:#6b7280;font-weight:800;">
                    لا توجد أرصدة مدينة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">أعلى أرصدة دائنة</h2>

    <table>
        <thead>
        <tr>
            <th style="width:35px;text-align:center;">م</th>
            <th>الكود</th>
            <th>{{ $partyLabel }}</th>
            <th class="text-left">الرصيد</th>
            <th class="text-left">الحركات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_credit'] ?? [] as $row)
            <tr>
                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['code'] }}</span></td>
                <td>{{ $row['name'] }}</td>
                <td class="text-left negative">{{ $row['closing_balance_label'] }}</td>
                <td class="text-left">{{ $row['rows_count'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:20px;color:#6b7280;font-weight:800;">
                    لا توجد أرصدة دائنة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="section-title">كل أرصدة {{ $partyPluralLabel }}</h2>

    <table class="compact-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-code">الكود</th>
            <th class="col-name">{{ $partyLabel }}</th>
            <th class="col-branch">الفرع</th>
            <th class="col-phone">تليفون</th>
            <th class="col-money text-left">افتتاحي</th>
            <th class="col-money text-left">مدين الفترة</th>
            <th class="col-money text-left">دائن الفترة</th>
            <th class="col-balance text-left">الرصيد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['rows'] ?? [] as $row)
            <tr>
                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="badge">{{ $row['code'] }}</span></td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['branch_name'] }}</td>
                <td>{{ $row['phone'] }}</td>
                <td class="text-left">{{ $money(abs((float) $row['opening_balance'])) }}</td>
                <td class="text-left positive">{{ $money($row['period_debit']) }}</td>
                <td class="text-left negative">{{ $money($row['period_credit']) }}</td>
                <td class="text-left {{ $balanceClass($row['balance_side']) }}">
                    {{ $row['closing_balance_label'] }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:20px;color:#6b7280;font-weight:800;">
                    لا توجد بيانات أرصدة.
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