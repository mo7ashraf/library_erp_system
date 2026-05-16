<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة مبيعات - {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Cairo", "Tahoma", sans-serif;
            direction: rtl;
            color: #111827;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
            font-size: 13px;
        }

        .receipt {
            width: 210mm;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 18px;
            border: 1px solid #e5e7eb;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .brand-title {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #6b7280;
        }

        .invoice-title {
            text-align: left;
        }

        .invoice-title h2 {
            margin: 0;
            font-size: 20px;
        }

        .invoice-title .number {
            margin-top: 6px;
            font-weight: 700;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 20px;
            margin-bottom: 16px;
        }

        .meta-box {
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 8px;
        }

        .meta-box h3 {
            margin: 0 0 8px;
            font-size: 14px;
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
            min-width: 110px;
        }

        .value {
            font-weight: 600;
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background: #f9fafb;
            font-weight: 800;
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
            width: 45%;
            margin-right: auto;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 0;
        }

        .totals .grand {
            font-size: 16px;
            font-weight: 900;
            border-bottom: 2px solid #111827;
        }

        .footer {
            margin-top: 28px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .signature {
            width: 45%;
            border-top: 1px solid #111827;
            padding-top: 8px;
            text-align: center;
            color: #374151;
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
            color: white;
            padding: 9px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-secondary {
            background: #6b7280;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .receipt {
                width: 100%;
                border: none;
                padding: 0;
            }

            .print-actions {
                display: none;
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

        <div class="invoice-title">
            <h2>فاتورة مبيعات</h2>
            <div class="number">رقم: {{ $invoice->invoice_number }}</div>
            <div>التاريخ: {{ optional($invoice->invoice_date)->format('Y-m-d') }}</div>
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta-box">
            <h3>بيانات العميل</h3>

            <div class="line">
                <span class="label">اسم العميل</span>
                <span class="value">{{ $invoice->customer?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">كود العميل</span>
                <span class="value">{{ $invoice->customer?->code ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">الموبايل</span>
                <span class="value">{{ $invoice->customer?->mobile ?? $invoice->customer?->phone ?? '-' }}</span>
            </div>
        </div>

        <div class="meta-box">
            <h3>بيانات الفاتورة</h3>

            <div class="line">
                <span class="label">المخزن</span>
                <span class="value">{{ $invoice->warehouse?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">الفرع</span>
                <span class="value">{{ $invoice->branch?->name ?? $invoice->warehouse?->branch?->name ?? '-' }}</span>
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