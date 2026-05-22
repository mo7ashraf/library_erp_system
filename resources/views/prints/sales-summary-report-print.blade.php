<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المبيعات</title>

    <x-erp.print-page-styles orientation="landscape" />
    <style>
        .sales-latest-invoices-table .col-small {
            width: 30px;
        }

        .sales-latest-invoices-table .col-date {
            width: 65px;
        }

        .sales-latest-invoices-table .col-number {
            width: 85px;
        }

        .sales-latest-invoices-table .col-customer {
            width: 130px;
        }

        .sales-latest-invoices-table .col-type {
            width: 70px;
        }

        .sales-latest-invoices-table .col-money {
            width: 75px;
        }
    </style>
</head>
<body>

@php
    $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    $totals = $report['totals'] ?? [];
@endphp

<div class="erp-print-actions">
    <button class="erp-print-btn" onclick="window.print()">طباعة</button>
    <button class="erp-print-btn erp-print-btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="erp-print-page">
    <div class="erp-print-header">
        <h1>تقرير المبيعات</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="erp-print-info-grid">
        <div class="erp-print-box">
            <h3 class="erp-print-box-title">بيانات التقرير</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">نوع التقرير</span>
                <span class="erp-print-value">تقرير ملخص المبيعات</span>
            </div>

            <div class="erp-print-line">
                <span class="erp-print-label">تاريخ الطباعة</span>
                <span class="erp-print-value">{{ now()->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="erp-print-box">
            <h3 class="erp-print-box-title">الفترة</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">من تاريخ</span>
                <span class="erp-print-value">{{ $fromDate ?? 'بداية النظام' }}</span>
            </div>

            <div class="erp-print-line">
                <span class="erp-print-label">إلى تاريخ</span>
                <span class="erp-print-value">{{ $toDate ?? 'حتى الآن' }}</span>
            </div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي المبيعات</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($totals['grand_total'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">عدد الفواتير</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $totals['invoices_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">متوسط قيمة الفاتورة</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['average_invoice_value'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي الخصومات</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($totals['discount_amount'] ?? 0) }}</div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي قبل الخصم</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['subtotal'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">خدمة</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['service_amount'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">عمولات</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['commission_amount'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مبيعات نقدية</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($totals['cash_total'] ?? 0) }}</div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مبيعات آجلة</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['credit_total'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مبيعات جزئية</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['partial_total'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">صافي بعد الخصم</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($totals['grand_total'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">الفترة</div>
            <div class="erp-print-summary-value erp-print-neutral" style="font-size: 12px;">
                {{ $fromDate ?? '-' }} → {{ $toDate ?? '-' }}
            </div>
        </div>
    </div>

    <h2 class="erp-print-section-title">المبيعات حسب نوع الدفع</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع الدفع</th>
            <th class="erp-print-text-left">عدد الفواتير</th>
            <th class="erp-print-text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['sales_by_payment_type'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['payment_type_label'] }}</span></td>
                <td class="erp-print-text-left">{{ $row['invoices_count'] }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['total_sales']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">المبيعات حسب نوع السعر</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع السعر</th>
            <th class="erp-print-text-left">عدد الفواتير</th>
            <th class="erp-print-text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['sales_by_price_type'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['price_type_label'] }}</span></td>
                <td class="erp-print-text-left">{{ $row['invoices_count'] }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['total_sales']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أفضل العملاء حسب المبيعات</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>العميل</th>
            <th class="erp-print-text-left">عدد الفواتير</th>
            <th class="erp-print-text-left">إجمالي المبيعات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_customers'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td class="erp-print-text-left">{{ $row['invoices_count'] }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['total_sales']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات عملاء في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">آخر فواتير المبيعات</h2>

    <table class="erp-print-table erp-print-compact-table sales-latest-invoices-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-date">التاريخ</th>
            <th class="col-number">رقم الفاتورة</th>
            <th class="col-customer">العميل</th>
            <th class="col-type">نوع الدفع</th>
            <th class="col-type">نوع السعر</th>
            <th class="col-money erp-print-text-left">قبل الخصم</th>
            <th class="col-money erp-print-text-left">الخصم</th>
            <th class="col-money erp-print-text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['latest_invoices'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['date'] }}</td>
                <td><span class="erp-print-badge">{{ $row['number'] }}</span></td>
                <td>{{ $row['customer'] }}</td>
                <td>{{ $row['payment_type'] }}</td>
                <td>{{ $row['price_type'] }}</td>
                <td class="erp-print-text-left">{{ $money($row['subtotal']) }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['discount_amount']) }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['grand_total']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد فواتير مبيعات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="erp-print-signatures">
        <div class="erp-print-signature">المحاسب</div>
        <div class="erp-print-signature">المراجع</div>
        <div class="erp-print-signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>