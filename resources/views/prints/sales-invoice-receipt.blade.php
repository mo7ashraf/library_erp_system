<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة مبيعات - {{ $invoice->invoice_number }}</title>
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
            color: #111827;
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
            width: 45%;
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
    <div class="header">
        <div>
            <h1 class="brand-title">نظام إدارة المكتبة</h1>
            <div class="brand-subtitle">
                فاتورة مبيعات / إيصال بيع
            </div>
        </div>

        <div class="receipt-title">
            <h2>فاتورة مبيعات</h2>
            <div class="number">رقم: {{ $invoice->invoice_number }}</div>
            <div>التاريخ: {{ optional($invoice->invoice_date)->format('Y-m-d') }}</div>
        </div>
    </div>
<div class="top-info">
    <div class="box">
        <h3>بيانات الفاتورة</h3>

        <div class="line">
            <span class="label">رقم الفاتورة</span>
            <span class="value">{{ $invoice->invoice_number }}</span>
        </div>

        <div class="line">
            <span class="label">تاريخ الفاتورة</span>
            <span class="value">{{ optional($invoice->invoice_date)->format('Y-m-d') }}</span>
        </div>

        <div class="line">
            <span class="label">طريقة السداد</span>
            <span class="value">
                @switch($invoice->payment_type)
                    @case('cash') نقدي @break
                    @case('credit') آجل @break
                    @case('partial') جزء نقدي / آجل @break
                    @default -
                @endswitch
            </span>
        </div>

        <div class="line">
            <span class="label">المستخدم</span>
            <span class="value">{{ $invoice->user?->name ?? '-' }}</span>
        </div>
    </div>

    <div class="box">
        <h3>بيانات العميل والمخزن</h3>

        <div class="line">
            <span class="label">العميل</span>
            <span class="value">{{ $invoice->customer?->name ?? '-' }}</span>
        </div>
        <div class="line">
                <span class="label">الموبايل</span>
                <span class="value">{{ $invoice->customer?->mobile ?? $invoice->customer?->phone ?? '-' }}</span>
            </div>
        <div class="line">
            <span class="label">كود العميل</span>
            <span class="value">{{ $invoice->customer?->code ?? '-' }}</span>
        </div>

        <div class="line">
            <span class="label">المخزن</span>
            <span class="value">{{ $invoice->warehouse?->name ?? '-' }}</span>
        </div>

        <div class="line">
            <span class="label">الفرع</span>
            <span class="value">{{ $invoice->branch?->name ?? $invoice->warehouse?->branch?->name ?? '-' }}</span>
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
            <th class="text-left">السعر</th>
            <th class="text-center">خصم %</th>
            <th class="text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $line)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $line->item?->code ?? '-' }}</td>
                <td>{{ $line->item?->name ?? '-' }}</td>
                <td>{{ $line->unit?->name ?? '-' }}</td>
                <td class="text-center">{{ number_format((float) $line->quantity, 3) }}</td>
                <td class="text-left">{{ number_format((float) $line->unit_price, 2) }}</td>
                <td class="text-center">{{ number_format((float) $line->discount_percent, 2) }}%</td>
                <td class="text-left">{{ number_format((float) $line->line_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>إجمالي الأصناف</span>
            <strong>{{ number_format((float) $invoice->subtotal, 2) }} ج.م</strong>
        </div>

        <div class="row">
            <span>خصم الفاتورة</span>
            <strong>{{ number_format((float) $invoice->discount_amount, 2) }} ج.م</strong>
        </div>

        <div class="row">
            <span>خدمات</span>
            <strong>{{ number_format((float) $invoice->service_amount, 2) }} ج.م</strong>
        </div>

        <div class="row">
            <span>قيمة العمولة</span>
            <strong>{{ number_format((float) $invoice->commission_amount, 2) }} ج.م</strong>
        </div>

        <div class="row grand">
            <span>الصافي النهائي</span>
            <strong>{{ number_format((float) $invoice->grand_total, 2) }} ج.م</strong>
        </div>
    </div>

    @if($invoice->notes)
        <div style="margin-top: 16px;">
            <strong>ملاحظات:</strong>
            <div>{{ $invoice->notes }}</div>
        </div>
    @endif

    <div class="footer">
        <div class="signature">توقيع المستلم</div>
        <div class="signature">توقيع المسؤول</div>
    </div>
</div>

</body>
</html>