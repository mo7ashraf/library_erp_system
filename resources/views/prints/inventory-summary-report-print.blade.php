<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المخزون</title>


    <x-erp.print-page-styles orientation="landscape" />

    <style>
        .inventory-wide-table .col-small {
            width: 30px;
        }

        .inventory-wide-table .col-date {
            width: 62px;
        }

        .inventory-wide-table .col-ref {
            width: 80px;
        }

        .inventory-wide-table .col-type {
            width: 95px;
        }

        .inventory-wide-table .col-name {
            width: 135px;
        }

        .inventory-wide-table .col-warehouse {
            width: 90px;
        }

        .inventory-wide-table .col-direction {
            width: 52px;
        }

        .inventory-wide-table .col-number {
            width: 70px;
        }
    </style>
</head>
<body>

@php
    $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    $number = fn ($value) => number_format((float) $value, 3);
    $totals = $report['totals'] ?? [];
@endphp

<div class="erp-print-actions">
    <button class="erp-print-btn" onclick="window.print()">طباعة</button>
    <button class="erp-print-btn erp-print-btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="erp-print-page">
    <div class="erp-print-header">
        <h1>تقرير المخزون</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="erp-print-info-grid">
        <div class="erp-print-box">
            <h3 class="erp-print-box-title">بيانات التقرير</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">نوع التقرير</span>
                <span class="erp-print-value">تقرير ملخص المخزون</span>
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
            <div class="erp-print-summary-label">قيمة المخزون الحالية</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($totals['total_value'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي الكمية</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $number($totals['total_quantity'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">أصناف لها رصيد</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $totals['items_with_stock_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مخازن بها أرصدة</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $totals['warehouses_count'] ?? 0 }}</div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">أصناف رصيدها صفر</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $totals['zero_stock_items_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">أصناف تحت حد الطلب</div>
            <div class="erp-print-summary-value erp-print-warning">{{ $totals['low_stock_items_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">الفترة من</div>
            <div class="erp-print-summary-value erp-print-neutral" style="font-size: 12px;">
                {{ $fromDate ?? '-' }}
            </div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">الفترة إلى</div>
            <div class="erp-print-summary-value erp-print-neutral" style="font-size: 12px;">
                {{ $toDate ?? '-' }}
            </div>
        </div>
    </div>

    <h2 class="erp-print-section-title">الأرصدة حسب المخزن</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>المخزن</th>
            <th class="erp-print-text-left">عدد الأصناف</th>
            <th class="erp-print-text-left">إجمالي الكمية</th>
            <th class="erp-print-text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['balances_by_warehouse'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['warehouse_name'] }}</td>
                <td class="erp-print-text-left">{{ $row['items_count'] }}</td>
                <td class="erp-print-text-left">{{ $number($row['total_quantity']) }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['total_value']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أرصدة مخزون.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أعلى الأصناف قيمة</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الكود</th>
            <th>الصنف</th>
            <th class="erp-print-text-left">الكمية</th>
            <th class="erp-print-text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_value_items'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['item_code'] }}</span></td>
                <td>{{ $row['item_name'] }}</td>
                <td class="erp-print-text-left">{{ $number($row['total_quantity']) }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['total_value']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أصناف لها قيمة مخزون.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أصناف تحت حد الطلب</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الكود</th>
            <th>الصنف</th>
            <th class="erp-print-text-left">الرصيد</th>
            <th class="erp-print-text-left">حد الطلب</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['low_stock_items'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['item_code'] }}</span></td>
                <td>{{ $row['item_name'] }}</td>
                <td class="erp-print-text-left erp-print-warning">{{ $number($row['stock_quantity']) }}</td>
                <td class="erp-print-text-left">{{ $number($row['threshold']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أصناف تحت حد الطلب.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أصناف رصيدها صفر</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الكود</th>
            <th>الصنف</th>
            <th class="erp-print-text-left">الرصيد</th>
            <th class="erp-print-text-left">حد الطلب</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['zero_stock_items'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['item_code'] }}</span></td>
                <td>{{ $row['item_name'] }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $number($row['stock_quantity']) }}</td>
                <td class="erp-print-text-left">{{ $number($row['reorder_level']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد أصناف رصيدها صفر.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">ملخص حركة المخزون</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع الحركة</th>
            <th>الاتجاه</th>
            <th class="erp-print-text-left">عدد الحركات</th>
            <th class="erp-print-text-left">الكمية</th>
            <th class="erp-print-text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['movement_summary'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['movement_type_label'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'erp-print-positive' : 'erp-print-negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="erp-print-text-left">{{ $row['movements_count'] }}</td>
                <td class="erp-print-text-left">{{ $number($row['total_quantity']) }}</td>
                <td class="erp-print-text-left">{{ $money($row['total_cost']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات مخزون خلال الفترة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">آخر حركات المخزون</h2>

    <table class="erp-print-table erp-print-compact-table inventory-wide-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-date">التاريخ</th>
            <th class="col-ref">المرجع</th>
            <th class="col-type">نوع الحركة</th>
            <th class="col-name">الصنف</th>
            <th class="col-warehouse">المخزن</th>
            <th class="col-direction">الاتجاه</th>
            <th class="col-number erp-print-text-left">الكمية</th>
            <th class="col-number erp-print-text-left">القيمة</th>
            <th class="col-number erp-print-text-left">الرصيد بعد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['latest_movements'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['reference_number'] }}</td>
                <td><span class="erp-print-badge">{{ $row['movement_type'] }}</span></td>
                <td>{{ $row['item'] }}</td>
                <td>{{ $row['warehouse'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'erp-print-positive' : 'erp-print-negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="erp-print-text-left">{{ $number($row['quantity']) }}</td>
                <td class="erp-print-text-left">{{ $money($row['total_cost']) }}</td>
                <td class="erp-print-text-left">{{ $number($row['balance_after']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات مخزون خلال الفترة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="erp-print-signatures">
        <div class="erp-print-signature">أمين المخزن</div>
        <div class="erp-print-signature">المراجع</div>
        <div class="erp-print-signature">اعتماد المسؤول</div>
    </div>
</div>

</body>
</html>