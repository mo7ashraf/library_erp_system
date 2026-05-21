<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المشتريات</title>


    <x-erp.print-page-styles orientation="landscape" />

    <style>
        .purchase-wide-table .col-small {
            width: 28px;
        }

        .purchase-wide-table .col-date {
            width: 62px;
        }

        .purchase-wide-table .col-number {
            width: 76px;
        }

        .purchase-wide-table .col-name {
            width: 110px;
        }

        .purchase-wide-table .col-payment {
            width: 55px;
        }

        .purchase-wide-table .col-money {
            width: 74px;
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
        <h1>تقرير المشتريات</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="erp-print-info-grid">
        <div class="erp-print-box">
            <h3 class="erp-print-box-title">بيانات التقرير</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">نوع التقرير</span>
                <span class="erp-print-value">تقرير ملخص المشتريات</span>
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
            <div class="erp-print-summary-label">إجمالي المشتريات</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($totals['grand_total'] ?? 0) }}</div>
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
            <div class="erp-print-summary-value erp-print-positive">{{ $money($totals['discount_amount'] ?? 0) }}</div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي قبل الخصم</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['subtotal'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">تكاليف إضافية</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($totals['additional_cost'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مشتريات نقدية</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($totals['cash_total'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مشتريات آجلة</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['credit_total'] ?? 0) }}</div>
        </div>
    </div>

    <h2 class="erp-print-section-title">المشتريات حسب نوع الدفع</h2>

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
        @forelse($report['purchases_by_payment_type'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['payment_type_label'] }}</span></td>
                <td class="erp-print-text-left">{{ $row['invoices_count'] }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['total_purchases']) }}</td>
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

    <h2 class="erp-print-section-title">المشتريات حسب المخزن</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>المخزن</th>
            <th class="erp-print-text-left">عدد الفواتير</th>
            <th class="erp-print-text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['purchases_by_warehouse'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['warehouse_name'] }}</td>
                <td class="erp-print-text-left">{{ $row['invoices_count'] }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['total_purchases']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات مخازن في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أفضل الموردين حسب المشتريات</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>المورد</th>
            <th class="erp-print-text-left">عدد الفواتير</th>
            <th class="erp-print-text-left">إجمالي المشتريات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_suppliers'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['supplier_name'] }}</td>
                <td class="erp-print-text-left">{{ $row['invoices_count'] }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['total_purchases']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات موردين في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">آخر فواتير المشتريات</h2>

    <table class="erp-print-table erp-print-compact-table purchase-wide-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-date">التاريخ</th>
            <th class="col-number">رقم الفاتورة</th>
            <th class="col-number">فاتورة المورد</th>
            <th class="col-name">المورد</th>
            <th class="col-name">المخزن</th>
            <th class="col-payment">الدفع</th>
            <th class="col-money erp-print-text-left">قبل الخصم</th>
            <th class="col-money erp-print-text-left">الخصم</th>
            <th class="col-money erp-print-text-left">إضافي</th>
            <th class="col-money erp-print-text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['latest_invoices'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['date'] }}</td>
                <td><span class="erp-print-badge">{{ $row['number'] }}</span></td>
                <td>{{ $row['supplier_invoice_number'] }}</td>
                <td>{{ $row['supplier'] }}</td>
                <td>{{ $row['warehouse'] }}</td>
                <td>{{ $row['payment_type'] }}</td>
                <td class="erp-print-text-left">{{ $money($row['subtotal']) }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['discount_amount']) }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['additional_cost']) }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['grand_total']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد فواتير مشتريات في الفترة المحددة.
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