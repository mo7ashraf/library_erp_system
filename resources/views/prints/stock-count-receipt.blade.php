<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>محضر جرد مخزن - {{ $document->count_number }}</title>


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
            background: white;
            padding: 16mm;
            border: 1px solid #d1d5db;
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
        }

        .subtitle {
            margin-top: 4px;
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

        .increase {
            color: #047857;
            font-weight: 800;
        }

        .decrease {
            color: #b91c1c;
            font-weight: 800;
        }

        .totals {
            margin-top: 16px;
            width: 55%;
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

        .grand {
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
            margin-top: 36px;
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
            body {
                background: white;
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
        <h1>محضر جرد مخزن</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="top-info">
        <div class="box">
            <h3>بيانات محضر الجرد</h3>

            <div class="line">
                <span class="label">رقم المحضر</span>
                <span class="value">{{ $document->count_number }}</span>
            </div>

            <div class="line">
                <span class="label">تاريخ الجرد</span>
                <span class="value">{{ optional($document->count_date)->format('Y-m-d') }}</span>
            </div>

            <div class="line">
                <span class="label">الحالة</span>
                <span class="value">
                    @switch($document->status)
                        @case('posted') مرحلة @break
                        @case('draft') مسودة @break
                        @default -
                    @endswitch
                </span>
            </div>

            <div class="line">
                <span class="label">المستخدم</span>
                <span class="value">{{ $document->user?->name ?? '-' }}</span>
            </div>
        </div>

        <div class="box">
            <h3>بيانات المخزن</h3>

            <div class="line">
                <span class="label">المخزن</span>
                <span class="value">{{ $document->warehouse?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">الفرع</span>
                <span class="value">{{ $document->branch?->name ?? $document->warehouse?->branch?->name ?? '-' }}</span>
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
            <th class="text-center">رصيد النظام</th>
            <th class="text-center">الرصيد الفعلي</th>
            <th class="text-center">الفرق</th>
            <th class="text-left">متوسط التكلفة</th>
            <th class="text-left">قيمة الفرق</th>
        </tr>
        </thead>

        <tbody>
        @foreach($document->items as $line)
            @php
                $difference = (float) $line->difference_quantity;
            @endphp

            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $line->item?->code ?? '-' }}</td>
                <td>{{ $line->item?->name ?? '-' }}</td>
                <td>{{ $line->unit?->name ?? '-' }}</td>
                <td class="text-center">{{ number_format((float) $line->system_quantity, 3) }}</td>
                <td class="text-center">{{ number_format((float) $line->actual_quantity, 3) }}</td>
                <td class="text-center {{ $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : '') }}">
                    {{ number_format($difference, 3) }}
                </td>
                <td class="text-left">{{ number_format((float) $line->unit_cost, 2) }}</td>
                <td class="text-left">{{ number_format((float) $line->difference_cost, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>إجمالي الزيادة</span>
            <strong class="increase">{{ number_format((float) $document->total_increase_quantity, 3) }}</strong>
        </div>

        <div class="row">
            <span>إجمالي العجز</span>
            <strong class="decrease">{{ number_format((float) $document->total_decrease_quantity, 3) }}</strong>
        </div>

        <div class="row grand">
            <span>إجمالي قيمة الفرق</span>
            <strong>{{ number_format((float) $document->total_difference_cost, 2) }} ج.م</strong>
        </div>
    </div>

    @if($document->notes)
        <div class="notes">
            <strong>ملاحظات:</strong>
            <div>{{ $document->notes }}</div>
        </div>
    @endif

    <div class="footer">
        <div class="signature">لجنة الجرد</div>
        <div class="signature">أمين المخزن</div>
        <div class="signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>