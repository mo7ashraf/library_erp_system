<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
        $totals = $report['totals'] ?? [];
    @endphp

    <style>
        .purchase-page {
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
        }

        .purchase-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .purchase-card-body {
            padding: 18px;
        }

        .purchase-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px;
        }

        .purchase-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.4;
        }

        .purchase-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }

        .purchase-filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .purchase-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .purchase-field input {
            width: 100%;
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            font-weight: 700;
            padding: 0 12px;
            outline: none;
        }

        .purchase-field input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
        }

        .purchase-actions {
            display: flex;
            gap: 8px;
        }

        .purchase-btn {
            height: 42px;
            border: none;
            border-radius: 12px;
            padding: 0 16px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .purchase-btn-primary {
            background: #f59e0b;
            color: #111827;
        }

        .purchase-btn-secondary {
            background: #6b7280;
            color: #ffffff;
        }

        .purchase-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .purchase-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .purchase-kpi-title {
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .purchase-kpi-value {
            margin-top: 10px;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.3;
            color: #111827;
        }

        .purchase-positive {
            color: #15803d;
            font-weight: 900;
        }

        .purchase-negative {
            color: #b91c1c;
            font-weight: 900;
        }

        .purchase-neutral {
            color: #374151;
            font-weight: 900;
        }

        .purchase-kpi-red {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .purchase-kpi-red .purchase-kpi-title,
        .purchase-kpi-red .purchase-kpi-value {
            color: #b91c1c;
        }

        .purchase-two-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .purchase-table-header {
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .purchase-table-title {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
            color: #111827;
        }

        .purchase-table-wrapper {
            overflow-x: auto;
        }

        .purchase-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .purchase-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .purchase-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .purchase-table tbody tr:hover {
            background: #fffbeb;
        }

        .purchase-table .text-left {
            text-align: left;
        }

        .purchase-badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .purchase-empty {
            text-align: center;
            padding: 28px 12px;
            color: #6b7280;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .purchase-filter-grid,
            .purchase-kpi-grid,
            .purchase-two-grid {
                grid-template-columns: 1fr;
            }

            .purchase-actions {
                width: 100%;
            }

            .purchase-btn {
                width: 100%;
            }
        }
    </style>

    <div class="purchase-page">
        <div class="purchase-card">
            <div class="purchase-header">
                <div>
                    <h2 class="purchase-title">تقرير المشتريات</h2>
                    <div class="purchase-subtitle">
                        ملخص المشتريات المرحلة حسب الفترة، نوع الدفع، الموردين، والمخازن.
                    </div>
                </div>
            </div>

            <div class="purchase-card-body">
                <form method="GET">
                    <div class="purchase-filter-grid">
                        <div class="purchase-field">
                            <label>من تاريخ</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}">
                        </div>

                        <div class="purchase-field">
                            <label>إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ $toDate }}">
                        </div>

                        <div class="purchase-actions">
                            <button type="submit" class="purchase-btn purchase-btn-primary">
                                عرض التقرير
                            </button>

                            <a
                                href="{{ route('admin.prints.purchase-summary-report', [
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                ]) }}"
                                target="_blank"
                                class="purchase-btn purchase-btn-secondary"
                            >
                                طباعة
                            </a>

                            <a href="{{ url()->current() }}" class="purchase-btn purchase-btn-secondary">
                                مسح
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="purchase-kpi-grid">
            <div class="purchase-kpi purchase-kpi-red">
                <div class="purchase-kpi-title">إجمالي المشتريات</div>
                <div class="purchase-kpi-value">{{ $money($totals['grand_total'] ?? 0) }}</div>
            </div>

            <div class="purchase-kpi">
                <div class="purchase-kpi-title">عدد الفواتير</div>
                <div class="purchase-kpi-value">{{ $totals['invoices_count'] ?? 0 }}</div>
            </div>

            <div class="purchase-kpi">
                <div class="purchase-kpi-title">متوسط قيمة الفاتورة</div>
                <div class="purchase-kpi-value">{{ $money($totals['average_invoice_value'] ?? 0) }}</div>
            </div>

            <div class="purchase-kpi">
                <div class="purchase-kpi-title">إجمالي الخصومات</div>
                <div class="purchase-kpi-value purchase-positive">{{ $money($totals['discount_amount'] ?? 0) }}</div>
            </div>
        </div>

        <div class="purchase-kpi-grid">
            <div class="purchase-kpi">
                <div class="purchase-kpi-title">إجمالي قبل الخصم</div>
                <div class="purchase-kpi-value">{{ $money($totals['subtotal'] ?? 0) }}</div>
            </div>

            <div class="purchase-kpi">
                <div class="purchase-kpi-title">تكاليف إضافية</div>
                <div class="purchase-kpi-value purchase-negative">{{ $money($totals['additional_cost'] ?? 0) }}</div>
            </div>

            <div class="purchase-kpi">
                <div class="purchase-kpi-title">مشتريات نقدية</div>
                <div class="purchase-kpi-value purchase-negative">{{ $money($totals['cash_total'] ?? 0) }}</div>
            </div>

            <div class="purchase-kpi">
                <div class="purchase-kpi-title">مشتريات آجلة</div>
                <div class="purchase-kpi-value purchase-neutral">{{ $money($totals['credit_total'] ?? 0) }}</div>
            </div>
        </div>

        <div class="purchase-two-grid">
            <div class="purchase-card">
                <div class="purchase-table-header">
                    <h3 class="purchase-table-title">المشتريات حسب نوع الدفع</h3>
                    <div class="purchase-subtitle">نقدي / آجل / جزئي.</div>
                </div>

                <div class="purchase-table-wrapper">
                    <table class="purchase-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>نوع الدفع</th>
                            <th class="text-left">عدد الفواتير</th>
                            <th class="text-left">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['purchases_by_payment_type'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td><span class="purchase-badge">{{ $row['payment_type_label'] }}</span></td>
                                <td class="text-left">{{ $row['invoices_count'] }}</td>
                                <td class="text-left purchase-negative">{{ $money($row['total_purchases']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="purchase-empty">لا توجد بيانات في الفترة المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="purchase-card">
                <div class="purchase-table-header">
                    <h3 class="purchase-table-title">المشتريات حسب المخزن</h3>
                    <div class="purchase-subtitle">توزيع المشتريات على المخازن.</div>
                </div>

                <div class="purchase-table-wrapper">
                    <table class="purchase-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>المخزن</th>
                            <th class="text-left">عدد الفواتير</th>
                            <th class="text-left">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['purchases_by_warehouse'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['warehouse_name'] }}</td>
                                <td class="text-left">{{ $row['invoices_count'] }}</td>
                                <td class="text-left purchase-negative">{{ $money($row['total_purchases']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="purchase-empty">لا توجد بيانات مخازن في الفترة المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="purchase-card">
            <div class="purchase-table-header">
                <h3 class="purchase-table-title">أفضل الموردين حسب المشتريات</h3>
                <div class="purchase-subtitle">أعلى 10 موردين في الفترة المحددة.</div>
            </div>

            <div class="purchase-table-wrapper">
                <table class="purchase-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>المورد</th>
                        <th class="text-left">عدد الفواتير</th>
                        <th class="text-left">إجمالي المشتريات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['top_suppliers'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td>{{ $row['supplier_name'] }}</td>
                            <td class="text-left">{{ $row['invoices_count'] }}</td>
                            <td class="text-left purchase-negative">{{ $money($row['total_purchases']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="purchase-empty">لا توجد بيانات موردين في الفترة المحددة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="purchase-card">
            <div class="purchase-table-header">
                <h3 class="purchase-table-title">آخر فواتير المشتريات</h3>
                <div class="purchase-subtitle">آخر 20 فاتورة مرحلة في الفترة المحددة.</div>
            </div>

            <div class="purchase-table-wrapper">
                <table class="purchase-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>التاريخ</th>
                        <th>رقم الفاتورة</th>
                        <th>رقم فاتورة المورد</th>
                        <th>المورد</th>
                        <th>المخزن</th>
                        <th>نوع الدفع</th>
                        <th class="text-left">قبل الخصم</th>
                        <th class="text-left">الخصم</th>
                        <th class="text-left">تكاليف إضافية</th>
                        <th class="text-left">الإجمالي</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['latest_invoices'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td><span class="purchase-badge">{{ $row['number'] }}</span></td>
                            <td>{{ $row['supplier_invoice_number'] }}</td>
                            <td>{{ $row['supplier'] }}</td>
                            <td>{{ $row['warehouse'] }}</td>
                            <td>{{ $row['payment_type'] }}</td>
                            <td class="text-left">{{ $money($row['subtotal']) }}</td>
                            <td class="text-left purchase-positive">{{ $money($row['discount_amount']) }}</td>
                            <td class="text-left purchase-negative">{{ $money($row['additional_cost']) }}</td>
                            <td class="text-left purchase-negative">{{ $money($row['grand_total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="purchase-empty">لا توجد فواتير مشتريات في الفترة المحددة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>