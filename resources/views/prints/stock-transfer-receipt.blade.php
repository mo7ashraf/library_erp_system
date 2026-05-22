<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إذن تحويل مخزني - {{ $transfer->transfer_number }}</title>


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
            font-family: "Cairo", "Tahoma", sans-serif;
            color: #111827;
            background: #e5e7eb;
            font-size: 13px;
        }

        body {
            padding: 20px;
        }

        .print-actions {
            width: 210mm;
            max-width: 100%;
            margin: 0 auto 12px;
            display: flex;
            justify-content: flex-start;
            gap: 8px;
        }

        .btn {
            border: none;
            background: #111827;
            color: #ffffff;
            padding: 9px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-family: "Cairo", "Tahoma", sans-serif;
            font-weight: 700;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .receipt {
            width: 210mm;
            min-height: 297mm;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 16mm;
            border: 1px solid #d1d5db;
            overflow: hidden;
        }

        .receipt-title {
            text-align: center;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #111827;
        }

        .receipt-title h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.4;
        }

        .receipt-title .subtitle {
            margin-top: 4px;
            font-size: 14px;
            color: #6b7280;
            font-weight: 600;
        }

        .top-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .box {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
        }

        .box h3 {
            margin: 0 0 8px;
            font-size: 14px;
            font-weight: 800;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
        }

        .line {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 5px 0;
        }

        .label {
            color: #6b7280;
            font-weight: 600;
        }

        .value {
            font-weight: 800;
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 7px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: 900;
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .totals {
            margin-top: 16px;
            width: 48%;
            margin-right: auto;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 12px;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 0;
        }

        .totals .row:last-child {
            border-bottom: none;
        }

        .totals .grand {
            font-size: 16px;
            font-weight: 900;
        }

        .notes {
            margin-top: 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
        }

        .footer {
            margin-top: 32px;
            display: flex;
            justify-content: space-between;
            gap: 24px;
        }

        .signature {
            width: 30%;
            border-top: 1px solid #111827;
            padding-top: 8px;
            text-align: center;
            font-weight: 700;
        }

        @media print {
            html,
            body {
                background: #ffffff;
                width: 210mm;
                min-height: 297mm;
            }

            body {
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            .receipt {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <button class="btn" onclick="window.print()">طباعة</button>
    <button class="btn btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="receipt">
    <div class="receipt-title">
        <h1>إذن تحويل مخزني</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="top-info">
        <div class="box">
            <h3>بيانات الإذن</h3>

            <div class="line">
                <span class="label">رقم الإذن</span>
                <span class="value">{{ $transfer->transfer_number }}</span>
            </div>

            <div class="line">
                <span class="label">تاريخ التحويل</span>
                <span class="value">{{ optional($transfer->transfer_date)->format('Y-m-d') }}</span>
            </div>

            <div class="line">
                <span class="label">الحالة</span>
                <span class="value">
                    @switch($transfer->status)
                        @case('posted') مرحلة @break
                        @case('draft') مسودة @break
                        @default -
                    @endswitch
                </span>
            </div>

            <div class="line">
                <span class="label">المستخدم</span>
                <span class="value">{{ $transfer->user?->name ?? '-' }}</span>
            </div>
        </div>

        <div class="box">
            <h3>بيانات المخازن</h3>

            <div class="line">
                <span class="label">من المخزن</span>
                <span class="value">{{ $transfer->fromWarehouse?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">من الفرع</span>
                <span class="value">{{ $transfer->fromBranch?->name ?? $transfer->fromWarehouse?->branch?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">إلى المخزن</span>
                <span class="value">{{ $transfer->toWarehouse?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">إلى الفرع</span>
                <span class="value">{{ $transfer->toBranch?->name ?? $transfer->toWarehouse?->branch?->name ?? '-' }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th class="text-center" style="width: 40px;">م</th>
            <th>كود الصنف</th>
            <th>اسم الصنف</th>
            <th>الوحدة</th>
            <th class="text-center">الكمية</th>
            <th class="text-left">تكلفة الوحدة</th>
            <th class="text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transfer->items as $line)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $line->item?->code ?? '-' }}</td>
                <td>{{ $line->item?->name ?? '-' }}</td>
                <td>{{ $line->unit?->name ?? '-' }}</td>
                <td class="text-center">{{ number_format((float) $line->quantity, 3) }}</td>
                <td class="text-left">{{ number_format((float) $line->unit_cost, 2) }}</td>
                <td class="text-left">{{ number_format((float) $line->total_cost, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>إجمالي الكمية</span>
            <strong>{{ number_format((float) $transfer->total_quantity, 3) }}</strong>
        </div>

        <div class="row grand">
            <span>إجمالي التكلفة</span>
            <strong>{{ number_format((float) $transfer->total_cost, 2) }} ج.م</strong>
        </div>
    </div>

    @if($transfer->notes)
        <div class="notes">
            <strong>ملاحظات:</strong>
            <div>{{ $transfer->notes }}</div>
        </div>
    @endif

    <div class="footer">
        <div class="signature">أمين المخزن الصادر</div>
        <div class="signature">أمين المخزن الوارد</div>
        <div class="signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>