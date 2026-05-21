<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مرتجع مبيعات - {{ $return->return_number }}</title>


    <style>
        @page { size: A4 portrait; margin: 12mm; }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            direction: rtl;
            font-family: "Cairo", "Tahoma", sans-serif;
            color: #111827;
            background: #e5e7eb;
            font-size: 13px;
        }

        body { padding: 20px; }

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

        .btn-secondary { background: #6b7280; }

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

        .label { color: #6b7280; font-weight: 600; }
        .value { font-weight: 800; text-align: left; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 7px;
        }

        th {
            background: #f3f4f6;
            font-weight: 900;
            text-align: right;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }

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

        .totals .row:last-child { border-bottom: none; }
        .grand { font-size: 16px; font-weight: 900; }

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
            body { background: white; padding: 0; }
            .print-actions { display: none; }
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
        <h1>مرتجع مبيعات</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="top-info">
        <div class="box">
            <h3>بيانات المرتجع</h3>

            <div class="line">
                <span class="label">رقم المرتجع</span>
                <span class="value">{{ $return->return_number }}</span>
            </div>

            <div class="line">
                <span class="label">التاريخ</span>
                <span class="value">{{ optional($return->return_date)->format('Y-m-d') }}</span>
            </div>

            <div class="line">
                <span class="label">فاتورة البيع</span>
                <span class="value">{{ $return->salesInvoice?->invoice_number ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">المستخدم</span>
                <span class="value">{{ $return->user?->name ?? '-' }}</span>
            </div>
        </div>

        <div class="box">
            <h3>بيانات العميل والمخزن</h3>

            <div class="line">
                <span class="label">العميل</span>
                <span class="value">{{ $return->customer?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">المخزن</span>
                <span class="value">{{ $return->warehouse?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">الفرع</span>
                <span class="value">{{ $return->branch?->name ?? $return->warehouse?->branch?->name ?? '-' }}</span>
            </div>

            <div class="line">
                <span class="label">رد القيمة</span>
                <span class="value">
                    @switch($return->refund_type)
                        @case('cash') رد نقدي @break
                        @case('credit_balance') إضافة إلى رصيد العميل @break
                        @default -
                    @endswitch
                </span>
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th class="text-center">م</th>
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
        @foreach($return->items as $line)
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
            <strong>{{ number_format((float) $return->subtotal, 2) }} ج.م</strong>
        </div>

        <div class="row">
            <span>خصم المرتجع</span>
            <strong>{{ number_format((float) $return->discount_amount, 2) }} ج.م</strong>
        </div>

        <div class="row grand">
            <span>صافي المرتجع</span>
            <strong>{{ number_format((float) $return->grand_total, 2) }} ج.م</strong>
        </div>
    </div>

    <div class="footer">
        <div class="signature">توقيع العميل</div>
        <div class="signature">توقيع المسؤول</div>
    </div>
</div>

</body>
</html>