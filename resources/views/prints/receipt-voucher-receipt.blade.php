<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سند قبض - {{ $voucher->voucher_number }}</title>


    <style>
        <x-erp.local-cairo-font />
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
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 2px solid #111827;
        }

        .receipt-title h1 {
            margin: 0;
            font-size: 26px;
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
            margin-bottom: 18px;
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
            margin: 6px 0;
        }

        .label { color: #6b7280; font-weight: 600; }
        .value { font-weight: 800; text-align: left; }

        .amount-box {
            margin: 20px 0;
            border: 2px solid #111827;
            border-radius: 10px;
            padding: 18px;
            text-align: center;
        }

        .amount-box .caption {
            color: #6b7280;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .amount-box .amount {
            font-size: 28px;
            font-weight: 900;
        }

        .description {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px;
            min-height: 80px;
            margin-top: 16px;
        }

        .footer {
            margin-top: 44px;
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
        <h1>سند قبض</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="top-info">
        <div class="box">
            <h3>بيانات السند</h3>

            <div class="line">
                <span class="label">رقم السند</span>
                <span class="value">{{ $voucher->voucher_number }}</span>
            </div>

            <div class="line">
                <span class="label">التاريخ</span>
                <span class="value">{{ optional($voucher->voucher_date)->format('Y-m-d') }}</span>
            </div>

            <div class="line">
                <span class="label">الحالة</span>
                <span class="value">
                    @switch($voucher->status)
                        @case('posted') مرحلة @break
                        @case('draft') مسودة @break
                        @default -
                    @endswitch
                </span>
            </div>

            <div class="line">
                <span class="label">المستخدم</span>
                <span class="value">{{ $voucher->user?->name ?? '-' }}</span>
            </div>
        </div>

        <div class="box">
            <h3>بيانات الطرف والتحصيل</h3>

            <div class="line">
                <span class="label">الطرف</span>
                <span class="value">{{ $voucher->resolvedPartyName() }}</span>
            </div>

            <div class="line">
                <span class="label">طريقة التحصيل</span>
                <span class="value">
                    @switch($voucher->payment_channel)
                        @case('cash') خزينة @break
                        @case('bank') بنك @break
                        @default -
                    @endswitch
                </span>
            </div>

            <div class="line">
                <span class="label">الخزينة / البنك</span>
                <span class="value">
                    {{ $voucher->cashbox?->name ?? $voucher->bankAccount?->account_name ?? '-' }}
                </span>
            </div>

            <div class="line">
                <span class="label">نوع السند</span>
                <span class="value">{{ $voucher->voucherTypeLabel() }}</span>
            </div>

            @if($voucher->category)
                <div class="line">
                    <span class="label">البند المالي</span>
                    <span class="value">{{ $voucher->category?->name ?? '-' }}</span>
                </div>
            @endif

            <div class="line">
                <span class="label">رقم الحركة المالية</span>
                <span class="value">{{ $voucher->treasuryTransaction?->transaction_number ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="amount-box">
        <div class="caption">المبلغ المقبوض</div>
        <div class="amount">{{ number_format((float) $voucher->amount, 2) }} ج.م</div>
    </div>

    <div class="description">
        <strong>البيان:</strong>
        <div>{{ $voucher->description ?? '-' }}</div>
    </div>

    @if($voucher->notes)
        <div class="description">
            <strong>ملاحظات:</strong>
            <div>{{ $voucher->notes }}</div>
        </div>
    @endif

    <div class="footer">
        <div class="signature">المستلم</div>
        <div class="signature">المراجع</div>
        <div class="signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>