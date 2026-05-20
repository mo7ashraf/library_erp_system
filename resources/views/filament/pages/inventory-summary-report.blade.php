<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
        $number = fn ($value) => number_format((float) $value, 3);
        $totals = $report['totals'] ?? [];
    @endphp

    <style>
        .inv-page {
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
        }

        .inv-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .inv-card-body {
            padding: 18px;
        }

        .inv-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px;
        }

        .inv-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.4;
        }

        .inv-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }

        .inv-filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .inv-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .inv-field input {
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

        .inv-field input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
        }

        .inv-actions {
            display: flex;
            gap: 8px;
        }

        .inv-btn {
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

        .inv-btn-primary {
            background: #f59e0b;
            color: #111827;
        }

        .inv-btn-secondary {
            background: #6b7280;
            color: #ffffff;
        }

        .inv-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .inv-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .inv-kpi-title {
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .inv-kpi-value {
            margin-top: 10px;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.3;
            color: #111827;
        }

        .inv-positive {
            color: #15803d;
            font-weight: 900;
        }

        .inv-negative {
            color: #b91c1c;
            font-weight: 900;
        }

        .inv-warning {
            color: #c2410c;
            font-weight: 900;
        }

        .inv-neutral {
            color: #374151;
            font-weight: 900;
        }

        .inv-kpi-green {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .inv-kpi-green .inv-kpi-title,
        .inv-kpi-green .inv-kpi-value {
            color: #15803d;
        }

        .inv-kpi-red {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .inv-kpi-red .inv-kpi-title,
        .inv-kpi-red .inv-kpi-value {
            color: #b91c1c;
        }

        .inv-two-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .inv-table-header {
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .inv-table-title {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
            color: #111827;
        }

        .inv-table-wrapper {
            overflow-x: auto;
        }

        .inv-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .inv-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .inv-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .inv-table tbody tr:hover {
            background: #fffbeb;
        }

        .inv-table .text-left {
            text-align: left;
        }

        .inv-badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .inv-empty {
            text-align: center;
            padding: 28px 12px;
            color: #6b7280;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .inv-filter-grid,
            .inv-kpi-grid,
            .inv-two-grid {
                grid-template-columns: 1fr;
            }

            .inv-actions {
                width: 100%;
            }

            .inv-btn {
                width: 100%;
            }
        }
    </style>

    <div class="inv-page">
        <div class="inv-card">
            <div class="inv-header">
                <div>
                    <h2 class="inv-title">تقرير المخزون</h2>
                    <div class="inv-subtitle">
                        ملخص أرصدة المخزون، قيمة المخزون، الأصناف الراكدة/المنخفضة، وحركة المخزون خلال الفترة.
                    </div>
                </div>
            </div>

            <div class="inv-card-body">
                <form method="GET">
                    <div class="inv-filter-grid">
                        <div class="inv-field">
                            <label>من تاريخ</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}">
                        </div>

                        <div class="inv-field">
                            <label>إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ $toDate }}">
                        </div>

                        <div class="inv-actions">
                            <button type="submit" class="inv-btn inv-btn-primary">عرض التقرير</button>

                            <a
                                href="{{ route('admin.prints.inventory-summary-report', [
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                ]) }}"
                                target="_blank"
                                class="inv-btn inv-btn-secondary"
                            >
                                طباعة
                            </a>

                            <a href="{{ url()->current() }}" class="inv-btn inv-btn-secondary">مسح</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="inv-kpi-grid">
            <div class="inv-kpi inv-kpi-green">
                <div class="inv-kpi-title">قيمة المخزون الحالية</div>
                <div class="inv-kpi-value">{{ $money($totals['total_value'] ?? 0) }}</div>
            </div>

            <div class="inv-kpi">
                <div class="inv-kpi-title">إجمالي الكمية</div>
                <div class="inv-kpi-value">{{ $number($totals['total_quantity'] ?? 0) }}</div>
            </div>

            <div class="inv-kpi">
                <div class="inv-kpi-title">أصناف لها رصيد</div>
                <div class="inv-kpi-value">{{ $totals['items_with_stock_count'] ?? 0 }}</div>
            </div>

            <div class="inv-kpi">
                <div class="inv-kpi-title">مخازن بها أرصدة</div>
                <div class="inv-kpi-value">{{ $totals['warehouses_count'] ?? 0 }}</div>
            </div>
        </div>

        <div class="inv-kpi-grid">
            <div class="inv-kpi inv-kpi-red">
                <div class="inv-kpi-title">أصناف رصيدها صفر</div>
                <div class="inv-kpi-value">{{ $totals['zero_stock_items_count'] ?? 0 }}</div>
            </div>

            <div class="inv-kpi">
                <div class="inv-kpi-title">أصناف تحت حد الطلب</div>
                <div class="inv-kpi-value inv-warning">{{ $totals['low_stock_items_count'] ?? 0 }}</div>
            </div>

            <div class="inv-kpi">
                <div class="inv-kpi-title">الفترة من</div>
                <div class="inv-kpi-value" style="font-size: 16px;">{{ $fromDate }}</div>
            </div>

            <div class="inv-kpi">
                <div class="inv-kpi-title">الفترة إلى</div>
                <div class="inv-kpi-value" style="font-size: 16px;">{{ $toDate }}</div>
            </div>
        </div>

        <div class="inv-two-grid">
            <div class="inv-card">
                <div class="inv-table-header">
                    <h3 class="inv-table-title">الأرصدة حسب المخزن</h3>
                    <div class="inv-subtitle">تجميع قيمة وكمية المخزون لكل مخزن.</div>
                </div>

                <div class="inv-table-wrapper">
                    <table class="inv-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>المخزن</th>
                            <th class="text-left">عدد الأصناف</th>
                            <th class="text-left">إجمالي الكمية</th>
                            <th class="text-left">القيمة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['balances_by_warehouse'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['warehouse_name'] }}</td>
                                <td class="text-left">{{ $row['items_count'] }}</td>
                                <td class="text-left">{{ $number($row['total_quantity']) }}</td>
                                <td class="text-left inv-positive">{{ $money($row['total_value']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="inv-empty">لا توجد أرصدة مخزون.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="inv-card">
                <div class="inv-table-header">
                    <h3 class="inv-table-title">أعلى الأصناف قيمة</h3>
                    <div class="inv-subtitle">أعلى 20 صنف حسب قيمة الرصيد الحالي.</div>
                </div>

                <div class="inv-table-wrapper">
                    <table class="inv-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>الكود</th>
                            <th>الصنف</th>
                            <th class="text-left">الكمية</th>
                            <th class="text-left">القيمة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['top_value_items'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td><span class="inv-badge">{{ $row['item_code'] }}</span></td>
                                <td>{{ $row['item_name'] }}</td>
                                <td class="text-left">{{ $number($row['total_quantity']) }}</td>
                                <td class="text-left inv-positive">{{ $money($row['total_value']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="inv-empty">لا توجد أصناف لها قيمة مخزون.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="inv-two-grid">
            <div class="inv-card">
                <div class="inv-table-header">
                    <h3 class="inv-table-title">أصناف تحت حد الطلب</h3>
                    <div class="inv-subtitle">أصناف رصيدها الحالي أقل من أو يساوي حد إعادة الطلب.</div>
                </div>

                <div class="inv-table-wrapper">
                    <table class="inv-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>الكود</th>
                            <th>الصنف</th>
                            <th class="text-left">الرصيد</th>
                            <th class="text-left">حد الطلب</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['low_stock_items'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td><span class="inv-badge">{{ $row['item_code'] }}</span></td>
                                <td>{{ $row['item_name'] }}</td>
                                <td class="text-left inv-warning">{{ $number($row['stock_quantity']) }}</td>
                                <td class="text-left">{{ $number($row['threshold']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="inv-empty">لا توجد أصناف تحت حد الطلب.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="inv-card">
                <div class="inv-table-header">
                    <h3 class="inv-table-title">أصناف رصيدها صفر</h3>
                    <div class="inv-subtitle">أول 20 صنف نشط بدون رصيد.</div>
                </div>

                <div class="inv-table-wrapper">
                    <table class="inv-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>الكود</th>
                            <th>الصنف</th>
                            <th class="text-left">الرصيد</th>
                            <th class="text-left">حد الطلب</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['zero_stock_items'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td><span class="inv-badge">{{ $row['item_code'] }}</span></td>
                                <td>{{ $row['item_name'] }}</td>
                                <td class="text-left inv-negative">{{ $number($row['stock_quantity']) }}</td>
                                <td class="text-left">{{ $number($row['reorder_level']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="inv-empty">لا توجد أصناف رصيدها صفر.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="inv-card">
            <div class="inv-table-header">
                <h3 class="inv-table-title">ملخص حركة المخزون</h3>
                <div class="inv-subtitle">تجميع الحركات حسب نوع الحركة والاتجاه خلال الفترة.</div>
            </div>

            <div class="inv-table-wrapper">
                <table class="inv-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>نوع الحركة</th>
                        <th>الاتجاه</th>
                        <th class="text-left">عدد الحركات</th>
                        <th class="text-left">الكمية</th>
                        <th class="text-left">القيمة</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['movement_summary'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td>{{ $row['movement_type_label'] }}</td>
                            <td>
                                <span class="{{ $row['direction'] === 'in' ? 'inv-positive' : 'inv-negative' }}">
                                    {{ $row['direction_label'] }}
                                </span>
                            </td>
                            <td class="text-left">{{ $row['movements_count'] }}</td>
                            <td class="text-left">{{ $number($row['total_quantity']) }}</td>
                            <td class="text-left">{{ $money($row['total_cost']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="inv-empty">لا توجد حركات مخزون خلال الفترة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="inv-card">
            <div class="inv-table-header">
                <h3 class="inv-table-title">آخر حركات المخزون</h3>
                <div class="inv-subtitle">آخر 30 حركة مخزون خلال الفترة.</div>
            </div>

            <div class="inv-table-wrapper">
                <table class="inv-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>التاريخ</th>
                        <th>المرجع</th>
                        <th>نوع الحركة</th>
                        <th>الصنف</th>
                        <th>المخزن</th>
                        <th>الاتجاه</th>
                        <th class="text-left">الكمية</th>
                        <th class="text-left">القيمة</th>
                        <th class="text-left">الرصيد بعد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['latest_movements'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['reference_number'] }}</td>
                            <td><span class="inv-badge">{{ $row['movement_type'] }}</span></td>
                            <td>{{ $row['item'] }}</td>
                            <td>{{ $row['warehouse'] }}</td>
                            <td>
                                <span class="{{ $row['direction'] === 'in' ? 'inv-positive' : 'inv-negative' }}">
                                    {{ $row['direction_label'] }}
                                </span>
                            </td>
                            <td class="text-left">{{ $number($row['quantity']) }}</td>
                            <td class="text-left">{{ $money($row['total_cost']) }}</td>
                            <td class="text-left">{{ $number($row['balance_after']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="inv-empty">لا توجد حركات مخزون خلال الفترة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>