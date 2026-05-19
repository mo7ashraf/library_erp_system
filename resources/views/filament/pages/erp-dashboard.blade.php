<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
        $number = fn ($value) => number_format((float) $value, 2);

        $sales = $dashboard['sales'] ?? [];
        $purchases = $dashboard['purchases'] ?? [];
        $treasury = $dashboard['treasury'] ?? [];
        $inventory = $dashboard['inventory'] ?? [];
        $periods = $dashboard['periods'] ?? [];

        $todayNetTreasury = (float) ($treasury['today_inflow'] ?? 0) - (float) ($treasury['today_outflow'] ?? 0);
        $monthNetTreasury = (float) ($treasury['month_inflow'] ?? 0) - (float) ($treasury['month_outflow'] ?? 0);

        $valueClass = function (float $value): string {
            if ($value > 0) {
                return 'dash-positive';
            }

            if ($value < 0) {
                return 'dash-negative';
            }

            return 'dash-neutral';
        };
    @endphp

    <style>
        .dash-page {
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
        }

        .dash-hero {
            background: linear-gradient(135deg, #111827 0%, #1f2937 55%, #f59e0b 100%);
            border-radius: 22px;
            padding: 24px;
            margin-bottom: 18px;
            color: #ffffff;
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.18);
        }

        .dash-hero h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.4;
        }

        .dash-hero p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 14px;
            font-weight: 700;
        }

        .dash-period {
            margin-top: 14px;
            display: inline-flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            padding: 8px 13px;
            font-size: 13px;
            font-weight: 900;
        }

        .dash-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .dash-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .dash-kpi-title {
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .dash-kpi-value {
            margin-top: 10px;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.3;
            color: #111827;
        }

        .dash-kpi-note {
            margin-top: 8px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
        }

        .dash-kpi-green {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .dash-kpi-green .dash-kpi-title,
        .dash-kpi-green .dash-kpi-value {
            color: #15803d;
        }

        .dash-kpi-red {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .dash-kpi-red .dash-kpi-title,
        .dash-kpi-red .dash-kpi-value {
            color: #b91c1c;
        }

        .dash-kpi-amber {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .dash-kpi-blue {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .dash-two-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .dash-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .dash-card-header {
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .dash-card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: #111827;
        }

        .dash-card-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
        }

        .dash-table-wrapper {
            overflow-x: auto;
        }

        .dash-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .dash-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .dash-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .dash-table tbody tr:hover {
            background: #fffbeb;
        }

        .dash-table .text-left {
            text-align: left;
        }

        .dash-badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dash-positive {
            color: #15803d;
            font-weight: 900;
        }

        .dash-negative {
            color: #b91c1c;
            font-weight: 900;
        }

        .dash-neutral {
            color: #374151;
            font-weight: 900;
        }

        .dash-empty {
            text-align: center;
            padding: 28px 12px;
            color: #6b7280;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .dash-kpi-grid,
            .dash-two-grid {
                grid-template-columns: 1fr;
            }

            .dash-hero h2 {
                font-size: 22px;
            }
        }
    </style>

    <div class="dash-page">
        <div class="dash-hero">
            <h2>لوحة التحكم التنفيذية</h2>
            <p>
                نظرة سريعة على المبيعات، المشتريات، حركة الخزينة والبنك، وقيمة المخزون.
            </p>

            <div class="dash-period">
                <span>اليوم: {{ $periods['today'] ?? '-' }}</span>
                <span>•</span>
                <span>الشهر: {{ $periods['month_start'] ?? '-' }} إلى {{ $periods['month_end'] ?? '-' }}</span>
            </div>
        </div>

        <div class="dash-kpi-grid">
            <div class="dash-kpi dash-kpi-green">
                <div class="dash-kpi-title">مبيعات اليوم</div>
                <div class="dash-kpi-value">{{ $money($sales['today_total'] ?? 0) }}</div>
                <div class="dash-kpi-note">عدد الفواتير: {{ $sales['today_count'] ?? 0 }}</div>
            </div>

            <div class="dash-kpi dash-kpi-red">
                <div class="dash-kpi-title">مشتريات اليوم</div>
                <div class="dash-kpi-value">{{ $money($purchases['today_total'] ?? 0) }}</div>
                <div class="dash-kpi-note">عدد الفواتير: {{ $purchases['today_count'] ?? 0 }}</div>
            </div>

            <div class="dash-kpi dash-kpi-amber">
                <div class="dash-kpi-title">صافي خزينة/بنك اليوم</div>
                <div class="dash-kpi-value {{ $valueClass($todayNetTreasury) }}">
                    {{ $money($todayNetTreasury) }}
                </div>
                <div class="dash-kpi-note">
                    داخل: {{ $money($treasury['today_inflow'] ?? 0) }} |
                    خارج: {{ $money($treasury['today_outflow'] ?? 0) }}
                </div>
            </div>

            <div class="dash-kpi dash-kpi-blue">
                <div class="dash-kpi-title">قيمة المخزون الحالية</div>
                <div class="dash-kpi-value">{{ $money($inventory['total_value'] ?? 0) }}</div>
                <div class="dash-kpi-note">
                    الكمية: {{ $number($inventory['total_quantity'] ?? 0) }} |
                    الأصناف: {{ $inventory['items_count'] ?? 0 }}
                </div>
            </div>
        </div>

        <div class="dash-kpi-grid">
            <div class="dash-kpi">
                <div class="dash-kpi-title">مبيعات الشهر</div>
                <div class="dash-kpi-value dash-positive">{{ $money($sales['month_total'] ?? 0) }}</div>
                <div class="dash-kpi-note">عدد الفواتير: {{ $sales['month_count'] ?? 0 }}</div>
            </div>

            <div class="dash-kpi">
                <div class="dash-kpi-title">مشتريات الشهر</div>
                <div class="dash-kpi-value dash-negative">{{ $money($purchases['month_total'] ?? 0) }}</div>
                <div class="dash-kpi-note">عدد الفواتير: {{ $purchases['month_count'] ?? 0 }}</div>
            </div>

            <div class="dash-kpi">
                <div class="dash-kpi-title">أرصدة الخزائن</div>
                <div class="dash-kpi-value">{{ $money($treasury['cashbox_balance'] ?? 0) }}</div>
                <div class="dash-kpi-note">إجمالي الرصيد الحالي للخزائن</div>
            </div>

            <div class="dash-kpi">
                <div class="dash-kpi-title">أرصدة البنوك</div>
                <div class="dash-kpi-value">{{ $money($treasury['bank_balance'] ?? 0) }}</div>
                <div class="dash-kpi-note">إجمالي الرصيد الحالي للحسابات البنكية</div>
            </div>
        </div>

        <div class="dash-two-grid">
            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h3 class="dash-card-title">آخر فواتير المبيعات</h3>
                        <div class="dash-card-subtitle">آخر 5 فواتير مبيعات مرحلة.</div>
                    </div>
                </div>

                <div class="dash-table-wrapper">
                    <table class="dash-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>التاريخ</th>
                            <th>رقم الفاتورة</th>
                            <th>العميل</th>
                            <th class="text-left">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sales['latest'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td><span class="dash-badge">{{ $row['number'] }}</span></td>
                                <td>{{ $row['party'] }}</td>
                                <td class="text-left dash-positive">{{ $money($row['amount']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="dash-empty">لا توجد فواتير مبيعات مرحلة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h3 class="dash-card-title">آخر فواتير المشتريات</h3>
                        <div class="dash-card-subtitle">آخر 5 فواتير مشتريات مرحلة.</div>
                    </div>
                </div>

                <div class="dash-table-wrapper">
                    <table class="dash-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>التاريخ</th>
                            <th>رقم الفاتورة</th>
                            <th>المورد</th>
                            <th class="text-left">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($purchases['latest'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td><span class="dash-badge">{{ $row['number'] }}</span></td>
                                <td>{{ $row['party'] }}</td>
                                <td class="text-left dash-negative">{{ $money($row['amount']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="dash-empty">لا توجد فواتير مشتريات مرحلة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="dash-two-grid">
            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h3 class="dash-card-title">آخر حركات الخزينة والبنك</h3>
                        <div class="dash-card-subtitle">آخر 8 حركات مالية.</div>
                    </div>
                </div>

                <div class="dash-table-wrapper">
                    <table class="dash-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>التاريخ</th>
                            <th>رقم الحركة</th>
                            <th>النوع</th>
                            <th>الحساب</th>
                            <th>الاتجاه</th>
                            <th class="text-left">المبلغ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($treasury['latest'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['number'] }}</td>
                                <td><span class="dash-badge">{{ $row['type'] }}</span></td>
                                <td>{{ $row['account'] }}</td>
                                <td>
                                    <span class="{{ $row['direction'] === 'in' ? 'dash-positive' : 'dash-negative' }}">
                                        {{ $row['direction_label'] }}
                                    </span>
                                </td>
                                <td class="text-left {{ $row['direction'] === 'in' ? 'dash-positive' : 'dash-negative' }}">
                                    {{ $money($row['amount']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="dash-empty">لا توجد حركات مالية.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h3 class="dash-card-title">أعلى الأصناف قيمة في المخزون</h3>
                        <div class="dash-card-subtitle">حسب إجمالي تكلفة الرصيد الحالي.</div>
                    </div>
                </div>

                <div class="dash-table-wrapper">
                    <table class="dash-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>الصنف</th>
                            <th>المخزن</th>
                            <th class="text-left">الكمية</th>
                            <th class="text-left">متوسط التكلفة</th>
                            <th class="text-left">إجمالي التكلفة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($inventory['top_value_items'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['item'] }}</td>
                                <td>{{ $row['warehouse'] }}</td>
                                <td class="text-left">{{ $number($row['quantity']) }}</td>
                                <td class="text-left">{{ $money($row['average_cost']) }}</td>
                                <td class="text-left dash-neutral">{{ $money($row['total_cost']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="dash-empty">لا توجد أرصدة مخزون حالية.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>