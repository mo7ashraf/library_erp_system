<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب عميل - {{ $ledger['party_name'] ?? '-' }}</title>


    <style>
        <x-erp.local-cairo-font />
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
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
            color: #111827;
        }

        .header .subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-weight: 700;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }

        .box {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 12px;
            background: #ffffff;
        }

        .box-title {
            margin: 0 0 10px;
            font-size: 15px;
            font-weight: 900;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 7px;
        }

        .line {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin: 7px 0;
        }

        .label {
            color: #6b7280;
            font-weight: 700;
            min-width: 95px;
        }

        .value {
            font-weight: 900;
            text-align: left;
            color: #111827;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }

        .summary-card {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 11px;
            background: #f9fafb;
        }

        .summary-label {
            color: #6b7280;
            font-size: 11.5px;
            font-weight: 800;
        }

        .summary-value {
            margin-top: 6px;
            font-size: 15.5px;
            font-weight: 900;
            color: #111827;
        }

        .debit {
            color: #047857;
            font-weight: 900;
        }

        .credit {
            color: #b91c1c;
            font-weight: 900;
        }

        .balance-debit {
            color: #c2410c;
            font-weight: 900;
        }

        .balance-credit {
            color: #1d4ed8;
            font-weight: 900;
        }

        .balance-zero {
            color: #374151;
            font-weight: 900;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #cfd6df;
            padding: 8px 7px;
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

        .reference {
            direction: ltr;
            font-family: Consolas, monospace;
            font-weight: 800;
            font-size: 11px;
            text-align: right;
            word-break: break-word;
        }

        .opening-row {
            background: #fff7ed;
            font-weight: 900;
        }

        .opening-row td {
            color: #111827;
        }

        tbody tr:nth-child(even):not(.opening-row) {
            background: #fcfcfd;
        }

        .footer-total td {
            background: #111827;
            color: white;
            font-weight: 900;
            font-size: 14px;
            border-color: #111827;
        }

        .footer-total .debit {
            color: #bbf7d0;
        }

        .footer-total .credit {
            color: #fecaca;
        }

        .footer-total .balance-debit,
        .footer-total .balance-credit,
        .footer-total .balance-zero {
            color: white;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 40px;
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

    $balanceClass = function (float $value): string {
        if ($value > 0) {
            return 'balance-debit';
        }

        if ($value < 0) {
            return 'balance-credit';
        }

        return 'balance-zero';
    };

    $openingBalance = (float) ($ledger['opening_balance'] ?? 0);
    $closingBalance = (float) ($ledger['closing_balance'] ?? 0);
@endphp

<div class="print-actions">
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="page">
    <div class="header">
        <h1>كشف حساب عميل</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="info-grid">
        <div class="box">
            <h3 class="box-title">بيانات العميل</h3>

            <div class="line">
                <span class="label">اسم العميل</span>
                <span class="value">{{ $ledger['party_name'] ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">كود العميل</span>
                <span class="value">{{ $ledger['party_code'] ?? '-' }}</span>
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
                <span class="value">{{ $ledger['from_date'] ?? 'بداية الحساب' }}</span>
            </div>

            <div class="line">
                <span class="label">إلى تاريخ</span>
                <span class="value">{{ $ledger['to_date'] ?? 'حتى الآن' }}</span>
            </div>

            <div class="line">
                <span class="label">عدد الحركات</span>
                <span class="value">{{ count($ledger['rows'] ?? []) }}</span>
            </div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">رصيد أول المدة</div>
            <div class="summary-value {{ $balanceClass($openingBalance) }}">
                {{ $ledger['opening_balance_label'] ?? '0.00' }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إجمالي المدين</div>
            <div class="summary-value debit">
                {{ $money($ledger['total_debit'] ?? 0) }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">إجمالي الدائن</div>
            <div class="summary-value credit">
                {{ $money($ledger['total_credit'] ?? 0) }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">الرصيد الختامي</div>
            <div class="summary-value {{ $balanceClass($closingBalance) }}">
                {{ $ledger['closing_balance_label'] ?? '0.00' }}
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width: 35px; text-align: center;">م</th>
            <th>التاريخ</th>
            <th>نوع المستند</th>
            <th>رقم المرجع</th>
            <th>البيان</th>
            <th class="text-left">مدين</th>
            <th class="text-left">دائن</th>
            <th class="text-left">الرصيد</th>
        </tr>
        </thead>

        <tbody>
        <tr class="opening-row">
            <td style="text-align: center; font-weight: 900;">0</td>
            <td>{{ $ledger['from_date'] ?? '-' }}</td>
            <td>رصيد افتتاحي</td>
            <td>-</td>
            <td>رصيد أول المدة للفترة المحددة</td>
            <td class="text-left">-</td>
            <td class="text-left">-</td>
            <td class="text-left {{ $balanceClass($openingBalance) }}">
                {{ $ledger['opening_balance_label'] ?? '0.00' }}
            </td>
        </tr>

        @forelse($ledger['rows'] as $row)
            @php
                $debit = (float) $row['debit'];
                $credit = (float) $row['credit'];
                $balance = (float) $row['balance'];
            @endphp

            <tr>
                <td style="text-align: center; font-weight: 900;">{{ $loop->iteration }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['document_type'] }}</td>
                <td class="reference">{{ $row['reference_number'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td class="text-left debit">{{ $debit > 0 ? $money($debit) : '-' }}</td>
                <td class="text-left credit">{{ $credit > 0 ? $money($credit) : '-' }}</td>
                <td class="text-left {{ $balanceClass($balance) }}">{{ $row['balance_label'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>

        <tfoot>
        <tr class="footer-total">
            <td colspan="5">الإجمالي</td>
            <td class="text-left">{{ $money($ledger['total_debit'] ?? 0) }}</td>
            <td class="text-left">{{ $money($ledger['total_credit'] ?? 0) }}</td>
            <td class="text-left">{{ $ledger['closing_balance_label'] ?? '0.00' }}</td>
        </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="signature">المحاسب</div>
        <div class="signature">المراجع</div>
        <div class="signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>