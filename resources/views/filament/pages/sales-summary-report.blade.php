<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
        $number = fn ($value) => number_format((float) $value, 2);

        $totals = $report['totals'] ?? [];
    @endphp

    <style>
        .sales-page {
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
        }

        .sales-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .sales-card-body {
            padding: 18px;
        }

        .sales-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px;
        }

        .sales-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.4;
        }

        .sales-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }

        .sales-filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .sales-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .sales-field input {
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

        .sales-field input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
        }

        .sales-actions {
            display: flex;
            gap: 8px;
        }

        .sales-btn {
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

        .sales-btn-primary {
            background: #f59e0b;
            color: #111827;
        }

        .sales-btn-secondary {
            background: #6b7280;
            color: #ffffff;
        }

        .sales-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .sales-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .sales-kpi-title {
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .sales-kpi-value {
            margin-top: 10px;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.3;
            color: #111827;
        }

        .sales-positive {
            color: #15803d;
            font-weight: 900;
        }

        .sales-negative {
            color: #b91c1c;
            font-weight: 900;
        }

        .sales-neutral {
            color: #374151;
            font-weight: 900;
        }

        .sales-kpi-green {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .sales-kpi-green .sales-kpi-title,
        .sales-kpi-green .sales-kpi-value {
            color: #15803d;
        }

        .sales-two-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .sales-table-header {
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sales-table-title {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
            color: #111827;
        }

        .sales-table-wrapper {
            overflow-x: auto;
        }

        .sales-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .sales-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .sales-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .sales-table tbody tr:hover {
            background: #fffbeb;
        }

        .sales-table .text-left {
            text-align: left;
        }

        .sales-badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .sales-empty {
            text-align: center;
            padding: 28px 12px;
            color: #6b7280;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .sales-filter-grid,
            .sales-kpi-grid,
            .sales-two-grid {
                grid-template-columns: 1fr;
            }

            .sales-actions {
                width: 100%;
            }

            .sales-btn {
                width: 100%;
            }
        }
    </style>

    <div class="sales-page">
        <div class="sales-card">
            <div class="sales-header">
                <div>
                    <h2 class="sales-title">تقرير المبيعات</h2>
                    <div class="sales-subtitle">
                        ملخص المبيعات المرحلة حسب الفترة، نوع الدفع، نوع السعر، والعملاء.
                    </div>
                </div>
            </div>

            <div class="sales-card-body">
                <form method="GET">
                    <div class="sales-filter-grid">
                        <div class="sales-field">
                            <label>من تاريخ</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}">
                        </div>

                        <div class="sales-field">
                            <label>إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ $toDate }}">
                        </div>

                        <div class="sales-actions">
                            <button type="submit" class="sales-btn sales-btn-primary">
                                عرض التقرير
                            </button>

                            <a
                                href="{{ route('admin.prints.sales-summary-report', [
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                ]) }}"
                                target="_blank"
                                class="sales-btn sales-btn-secondary"
                            >
                                طباعة
                            </a>

                            <a href="{{ url()->current() }}" class="sales-btn sales-btn-secondary">
                                مسح
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="sales-kpi-grid">
            <div class="sales-kpi sales-kpi-green">
                <div class="sales-kpi-title">إجمالي المبيعات</div>
                <div class="sales-kpi-value">{{ $money($totals['grand_total'] ?? 0) }}</div>
            </div>

            <div class="sales-kpi">
                <div class="sales-kpi-title">عدد الفواتير</div>
                <div class="sales-kpi-value">{{ $totals['invoices_count'] ?? 0 }}</div>
            </div>

            <div class="sales-kpi">
                <div class="sales-kpi-title">متوسط قيمة الفاتورة</div>
                <div class="sales-kpi-value">{{ $money($totals['average_invoice_value'] ?? 0) }}</div>
            </div>

            <div class="sales-kpi">
                <div class="sales-kpi-title">إجمالي الخصومات</div>
                <div class="sales-kpi-value sales-negative">{{ $money($totals['discount_amount'] ?? 0) }}</div>
            </div>
        </div>

        <div class="sales-kpi-grid">
            <div class="sales-kpi">
                <div class="sales-kpi-title">إجمالي قبل الخصم</div>
                <div class="sales-kpi-value">{{ $money($totals['subtotal'] ?? 0) }}</div>
            </div>

            <div class="sales-kpi">
                <div class="sales-kpi-title">خدمة</div>
                <div class="sales-kpi-value">{{ $money($totals['service_amount'] ?? 0) }}</div>
            </div>

            <div class="sales-kpi">
                <div class="sales-kpi-title">عمولات</div>
                <div class="sales-kpi-value">{{ $money($totals['commission_amount'] ?? 0) }}</div>
            </div>

            <div class="sales-kpi">
                <div class="sales-kpi-title">مبيعات نقدية</div>
                <div class="sales-kpi-value sales-positive">{{ $money($totals['cash_total'] ?? 0) }}</div>
            </div>
        </div>

        <div class="sales-two-grid">
            <div class="sales-card">
                <div class="sales-table-header">
                    <h3 class="sales-table-title">المبيعات حسب نوع الدفع</h3>
                    <div class="sales-subtitle">نقدي / آجل / جزئي.</div>
                </div>

                <div class="sales-table-wrapper">
                    <table class="sales-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>نوع الدفع</th>
                            <th class="text-left">عدد الفواتير</th>
                            <th class="text-left">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['sales_by_payment_type'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td><span class="sales-badge">{{ $row['payment_type_label'] }}</span></td>
                                <td class="text-left">{{ $row['invoices_count'] }}</td>
                                <td class="text-left sales-positive">{{ $money($row['total_sales']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="sales-empty">لا توجد بيانات في الفترة المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="sales-card">
                <div class="sales-table-header">
                    <h3 class="sales-table-title">المبيعات حسب نوع السعر</h3>
                    <div class="sales-subtitle">طالب / معلم / مندوب / قطاعي / جملة.</div>
                </div>

                <div class="sales-table-wrapper">
                    <table class="sales-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>نوع السعر</th>
                            <th class="text-left">عدد الفواتير</th>
                            <th class="text-left">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['sales_by_price_type'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td><span class="sales-badge">{{ $row['price_type_label'] }}</span></td>
                                <td class="text-left">{{ $row['invoices_count'] }}</td>
                                <td class="text-left sales-positive">{{ $money($row['total_sales']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="sales-empty">لا توجد بيانات في الفترة المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="sales-card">
            <div class="sales-table-header">
                <h3 class="sales-table-title">أفضل العملاء حسب المبيعات</h3>
                <div class="sales-subtitle">أعلى 10 عملاء في الفترة المحددة.</div>
            </div>

            <div class="sales-table-wrapper">
                <table class="sales-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>العميل</th>
                        <th class="text-left">عدد الفواتير</th>
                        <th class="text-left">إجمالي المبيعات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['top_customers'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td>{{ $row['customer_name'] }}</td>
                            <td class="text-left">{{ $row['invoices_count'] }}</td>
                            <td class="text-left sales-positive">{{ $money($row['total_sales']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="sales-empty">لا توجد بيانات عملاء في الفترة المحددة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sales-card">
            <div class="sales-table-header">
                <h3 class="sales-table-title">آخر فواتير المبيعات</h3>
                <div class="sales-subtitle">آخر 20 فاتورة مرحلة في الفترة المحددة.</div>
            </div>

            <div class="sales-table-wrapper">
                <table class="sales-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>التاريخ</th>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>نوع الدفع</th>
                        <th>نوع السعر</th>
                        <th class="text-left">قبل الخصم</th>
                        <th class="text-left">الخصم</th>
                        <th class="text-left">الإجمالي</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['latest_invoices'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td><span class="sales-badge">{{ $row['number'] }}</span></td>
                            <td>{{ $row['customer'] }}</td>
                            <td>{{ $row['payment_type'] }}</td>
                            <td>{{ $row['price_type'] }}</td>
                            <td class="text-left">{{ $money($row['subtotal']) }}</td>
                            <td class="text-left sales-negative">{{ $money($row['discount_amount']) }}</td>
                            <td class="text-left sales-positive">{{ $money($row['grand_total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="sales-empty">لا توجد فواتير مبيعات في الفترة المحددة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>