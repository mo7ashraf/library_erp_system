<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';

        $totalInflow = (float) ($report['total_inflow'] ?? 0);
        $totalOutflow = (float) ($report['total_outflow'] ?? 0);
        $netMovement = $totalInflow - $totalOutflow;

        $valueClass = function (float $value): string {
            if ($value > 0) {
                return 'fin-positive';
            }

            if ($value < 0) {
                return 'fin-negative';
            }

            return 'fin-neutral';
        };
    @endphp

    <style>
        .fin-page {
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
        }

        .fin-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .fin-card-body {
            padding: 18px;
        }

        .fin-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px;
        }

        .fin-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.4;
        }

        .fin-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }

        .fin-filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .fin-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .fin-field input {
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

        .fin-field input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
        }

        .fin-actions {
            display: flex;
            gap: 8px;
        }

        .fin-btn {
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

        .fin-btn-primary {
            background: #f59e0b;
            color: #111827;
        }

        .fin-btn-secondary {
            background: #6b7280;
            color: #ffffff;
        }

        .fin-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .fin-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .fin-kpi-title {
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .fin-kpi-value {
            margin-top: 10px;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.3;
        }

        .fin-kpi-in {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .fin-kpi-in .fin-kpi-title,
        .fin-kpi-in .fin-kpi-value {
            color: #15803d;
        }

        .fin-kpi-out {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .fin-kpi-out .fin-kpi-title,
        .fin-kpi-out .fin-kpi-value {
            color: #b91c1c;
        }

        .fin-kpi-net {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .fin-positive {
            color: #15803d;
            font-weight: 900;
        }

        .fin-negative {
            color: #b91c1c;
            font-weight: 900;
        }

        .fin-neutral {
            color: #374151;
            font-weight: 900;
        }

        .fin-two-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .fin-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .fin-table-title {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
        }

        .fin-table-wrapper {
            overflow-x: auto;
        }

        .fin-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .fin-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .fin-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .fin-table tbody tr:hover {
            background: #fffbeb;
        }

        .fin-table .text-left {
            text-align: left;
        }

        .fin-badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .fin-empty {
            text-align: center;
            padding: 28px 12px;
            color: #6b7280;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .fin-filter-grid,
            .fin-kpi-grid,
            .fin-two-grid {
                grid-template-columns: 1fr;
            }

            .fin-actions {
                width: 100%;
            }

            .fin-btn {
                width: 100%;
            }
        }

        @media print {
            .fi-sidebar,
            .fi-topbar,
            .fin-card:first-child {
                display: none !important;
            }

            .fin-card,
            .fin-kpi {
                box-shadow: none;
            }
        }
    </style>

    <div class="fin-page">
        <div class="fin-card">
            <div class="fin-header">
                <div>
                    <h2 class="fin-title">الملخص المالي</h2>
                    <div class="fin-subtitle">
                        تقرير مجمع لحركة الخزائن والبنوك خلال فترة محددة.
                    </div>
                </div>
            </div>

            <div class="fin-card-body">
                <form method="GET">
                    <div class="fin-filter-grid">
                        <div class="fin-field">
                            <label>من تاريخ</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}">
                        </div>

                        <div class="fin-field">
                            <label>إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ $toDate }}">
                        </div>

                        <div class="fin-actions">
                            <button type="submit" class="fin-btn fin-btn-primary">
                                عرض التقرير
                            </button>

                            <button type="button" onclick="window.print()" class="fin-btn fin-btn-secondary">
                                طباعة
                            </button>

                            <a href="{{ url()->current() }}" class="fin-btn fin-btn-secondary">
                                مسح
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="fin-kpi-grid">
            <div class="fin-kpi fin-kpi-in">
                <div class="fin-kpi-title">إجمالي الداخل</div>
                <div class="fin-kpi-value">{{ $money($totalInflow) }}</div>
            </div>

            <div class="fin-kpi fin-kpi-out">
                <div class="fin-kpi-title">إجمالي الخارج</div>
                <div class="fin-kpi-value">{{ $money($totalOutflow) }}</div>
            </div>

            <div class="fin-kpi fin-kpi-net">
                <div class="fin-kpi-title">صافي الحركة</div>
                <div class="fin-kpi-value {{ $valueClass($netMovement) }}">
                    {{ $money($netMovement) }}
                </div>
            </div>

            <div class="fin-kpi">
                <div class="fin-kpi-title">إجمالي الأرصدة الحالية</div>
                <div class="fin-kpi-value">
                    {{ $money(($report['cashbox_total_balance'] ?? 0) + ($report['bank_total_balance'] ?? 0)) }}
                </div>
            </div>
        </div>

        <div class="fin-kpi-grid">
            <div class="fin-kpi">
                <div class="fin-kpi-title">داخل الخزائن</div>
                <div class="fin-kpi-value fin-positive">
                    {{ $money($report['cash_inflow'] ?? 0) }}
                </div>
            </div>

            <div class="fin-kpi">
                <div class="fin-kpi-title">خارج الخزائن</div>
                <div class="fin-kpi-value fin-negative">
                    {{ $money($report['cash_outflow'] ?? 0) }}
                </div>
            </div>

            <div class="fin-kpi">
                <div class="fin-kpi-title">داخل البنوك</div>
                <div class="fin-kpi-value fin-positive">
                    {{ $money($report['bank_inflow'] ?? 0) }}
                </div>
            </div>

            <div class="fin-kpi">
                <div class="fin-kpi-title">خارج البنوك</div>
                <div class="fin-kpi-value fin-negative">
                    {{ $money($report['bank_outflow'] ?? 0) }}
                </div>
            </div>
        </div>

        <div class="fin-two-grid">
            <div class="fin-card">
                <div class="fin-table-header">
                    <div>
                        <h3 class="fin-table-title">ملخص أنواع الحركات</h3>
                        <div class="fin-subtitle">تجميع حسب نوع الحركة والاتجاه.</div>
                    </div>
                </div>

                <div class="fin-table-wrapper">
                    <table class="fin-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>نوع الحركة</th>
                            <th>الاتجاه</th>
                            <th class="text-left">عدد الحركات</th>
                            <th class="text-left">الإجمالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['transaction_type_summary'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['transaction_type_label'] }}</td>
                                <td>
                                    <span class="{{ $row['direction'] === 'in' ? 'fin-positive' : 'fin-negative' }}">
                                        {{ $row['direction_label'] }}
                                    </span>
                                </td>
                                <td class="text-left">{{ $row['transactions_count'] }}</td>
                                <td class="text-left {{ $row['direction'] === 'in' ? 'fin-positive' : 'fin-negative' }}">
                                    {{ $money($row['total_amount']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="fin-empty">لا توجد حركات في الفترة المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="fin-card">
                <div class="fin-table-header">
                    <div>
                        <h3 class="fin-table-title">آخر الحركات المالية</h3>
                        <div class="fin-subtitle">آخر 20 حركة خلال الفترة.</div>
                    </div>
                </div>

                <div class="fin-table-wrapper">
                    <table class="fin-table">
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
                        @forelse($report['latest_transactions'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['transaction_date'] }}</td>
                                <td>{{ $row['transaction_number'] }}</td>
                                <td><span class="fin-badge">{{ $row['transaction_type_label'] }}</span></td>
                                <td>{{ $row['account_name'] }}</td>
                                <td>
                                    <span class="{{ $row['direction'] === 'in' ? 'fin-positive' : 'fin-negative' }}">
                                        {{ $row['direction_label'] }}
                                    </span>
                                </td>
                                <td class="text-left {{ $row['direction'] === 'in' ? 'fin-positive' : 'fin-negative' }}">
                                    {{ $money($row['amount']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="fin-empty">لا توجد حركات مالية في الفترة المحددة.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="fin-two-grid">
            <div class="fin-card">
                <div class="fin-table-header">
                    <div>
                        <h3 class="fin-table-title">أرصدة الخزائن</h3>
                        <div class="fin-subtitle">الرصيد الحالي وحركة الفترة لكل خزينة.</div>
                    </div>
                </div>

                <div class="fin-table-wrapper">
                    <table class="fin-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>الخزينة</th>
                            <th>الفرع</th>
                            <th class="text-left">داخل الفترة</th>
                            <th class="text-left">خارج الفترة</th>
                            <th class="text-left">الرصيد الحالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['cashboxes'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['branch_name'] }}</td>
                                <td class="text-left fin-positive">{{ $money($row['period_in']) }}</td>
                                <td class="text-left fin-negative">{{ $money($row['period_out']) }}</td>
                                <td class="text-left fin-neutral">{{ $money($row['current_balance']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="fin-empty">لا توجد خزائن.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="fin-card">
                <div class="fin-table-header">
                    <div>
                        <h3 class="fin-table-title">أرصدة البنوك</h3>
                        <div class="fin-subtitle">الرصيد الحالي وحركة الفترة لكل حساب بنكي.</div>
                    </div>
                </div>

                <div class="fin-table-wrapper">
                    <table class="fin-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>الحساب</th>
                            <th>البنك</th>
                            <th class="text-left">داخل الفترة</th>
                            <th class="text-left">خارج الفترة</th>
                            <th class="text-left">الرصيد الحالي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['bank_accounts'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['account_name'] }}</td>
                                <td>{{ $row['bank_name'] }}</td>
                                <td class="text-left fin-positive">{{ $money($row['period_in']) }}</td>
                                <td class="text-left fin-negative">{{ $money($row['period_out']) }}</td>
                                <td class="text-left fin-neutral">{{ $money($row['current_balance']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="fin-empty">لا توجد حسابات بنكية.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>