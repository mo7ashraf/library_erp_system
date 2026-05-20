@php
    $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    $totals = $report['totals'] ?? [];

    $balanceClass = function (?string $side): string {
        return match ($side) {
            'debit' => 'party-positive',
            'credit' => 'party-negative',
            default => 'party-neutral',
        };
    };
@endphp

<style>
    .party-page {
        direction: rtl;
        font-family: "Cairo", Tahoma, Arial, sans-serif;
        color: #111827;
    }

    .party-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        margin-bottom: 18px;
        overflow: hidden;
    }

    .party-card-body {
        padding: 18px;
    }

    .party-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px;
    }

    .party-title {
        margin: 0;
        font-size: 24px;
        font-weight: 900;
        color: #111827;
        line-height: 1.4;
    }

    .party-subtitle {
        margin-top: 4px;
        color: #6b7280;
        font-size: 14px;
        font-weight: 600;
    }

    .party-filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    .party-field label {
        display: block;
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: 900;
        color: #374151;
    }

    .party-field input {
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

    .party-field input:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
    }

    .party-actions {
        display: flex;
        gap: 8px;
    }

    .party-btn {
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

    .party-btn-primary {
        background: #f59e0b;
        color: #111827;
    }

    .party-btn-secondary {
        background: #6b7280;
        color: #ffffff;
    }

    .party-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .party-kpi {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .party-kpi-title {
        font-size: 13px;
        font-weight: 900;
        color: #6b7280;
    }

    .party-kpi-value {
        margin-top: 10px;
        font-size: 22px;
        font-weight: 900;
        line-height: 1.3;
        color: #111827;
    }

    .party-positive {
        color: #15803d;
        font-weight: 900;
    }

    .party-negative {
        color: #b91c1c;
        font-weight: 900;
    }

    .party-neutral {
        color: #374151;
        font-weight: 900;
    }

    .party-kpi-green {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .party-kpi-green .party-kpi-title,
    .party-kpi-green .party-kpi-value {
        color: #15803d;
    }

    .party-kpi-red {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .party-kpi-red .party-kpi-title,
    .party-kpi-red .party-kpi-value {
        color: #b91c1c;
    }

    .party-two-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    .party-table-header {
        padding: 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .party-table-title {
        font-size: 18px;
        font-weight: 900;
        margin: 0;
        color: #111827;
    }

    .party-table-wrapper {
        overflow-x: auto;
    }

    .party-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        font-size: 14px;
    }

    .party-table th {
        background: #f9fafb;
        color: #374151;
        font-size: 13px;
        font-weight: 900;
        text-align: right;
        padding: 13px 14px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .party-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .party-table tbody tr:hover {
        background: #fffbeb;
    }

    .party-table .text-left {
        text-align: left;
    }

    .party-badge {
        display: inline-flex;
        background: #f3f4f6;
        color: #374151;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .party-link {
        color: #2563eb;
        font-weight: 900;
        text-decoration: none;
    }

    .party-link:hover {
        text-decoration: underline;
    }

    .party-empty {
        text-align: center;
        padding: 28px 12px;
        color: #6b7280;
        font-weight: 800;
    }

    @media (max-width: 1100px) {
        .party-filter-grid,
        .party-kpi-grid,
        .party-two-grid {
            grid-template-columns: 1fr;
        }

        .party-actions {
            width: 100%;
        }

        .party-btn {
            width: 100%;
        }
    }
</style>

<div class="party-page">
    <div class="party-card">
        <div class="party-header">
            <div>
                <h2 class="party-title">{{ $report['title'] ?? 'تقرير الأرصدة' }}</h2>
                <div class="party-subtitle">
                    تقرير مجمع لأرصدة {{ $partyPluralLabel }} باستخدام كشوف الحساب التفصيلية.
                </div>
            </div>
        </div>

        <div class="party-card-body">
            <form method="GET">
                <div class="party-filter-grid">
                    <div class="party-field">
                        <label>من تاريخ</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}">
                    </div>

                    <div class="party-field">
                        <label>إلى تاريخ</label>
                        <input type="date" name="to_date" value="{{ $toDate }}">
                    </div>

                    <div class="party-actions">
                        <button type="submit" class="party-btn party-btn-primary">عرض التقرير</button>

                        @if($printRoute)
                            <a
                                href="{{ $printRoute }}"
                                target="_blank"
                                class="party-btn party-btn-secondary"
                            >
                                طباعة
                            </a>
                        @endif

                        <a href="{{ url()->current() }}" class="party-btn party-btn-secondary">مسح</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="party-kpi-grid">
        <div class="party-kpi">
            <div class="party-kpi-title">عدد {{ $partyPluralLabel }}</div>
            <div class="party-kpi-value">{{ $totals['parties_count'] ?? 0 }}</div>
        </div>

        <div class="party-kpi party-kpi-green">
            <div class="party-kpi-title">أرصدة مدينة</div>
            <div class="party-kpi-value">{{ $money($totals['closing_debit_total'] ?? 0) }}</div>
        </div>

        <div class="party-kpi party-kpi-red">
            <div class="party-kpi-title">أرصدة دائنة</div>
            <div class="party-kpi-value">{{ $money($totals['closing_credit_total'] ?? 0) }}</div>
        </div>

        <div class="party-kpi">
            <div class="party-kpi-title">صافي الرصيد</div>
            <div class="party-kpi-value {{ ($totals['net_balance'] ?? 0) >= 0 ? 'party-positive' : 'party-negative' }}">
                {{ $money(abs((float) ($totals['net_balance'] ?? 0))) }}
                {{ ($totals['net_balance'] ?? 0) >= 0 ? 'مدين' : 'دائن' }}
            </div>
        </div>
    </div>

    <div class="party-kpi-grid">
        <div class="party-kpi">
            <div class="party-kpi-title">عدد أصحاب الأرصدة المدينة</div>
            <div class="party-kpi-value party-positive">{{ $totals['debit_parties_count'] ?? 0 }}</div>
        </div>

        <div class="party-kpi">
            <div class="party-kpi-title">عدد أصحاب الأرصدة الدائنة</div>
            <div class="party-kpi-value party-negative">{{ $totals['credit_parties_count'] ?? 0 }}</div>
        </div>

        <div class="party-kpi">
            <div class="party-kpi-title">أرصدة صفرية</div>
            <div class="party-kpi-value party-neutral">{{ $totals['zero_parties_count'] ?? 0 }}</div>
        </div>

        <div class="party-kpi">
            <div class="party-kpi-title">حركة الفترة</div>
            <div class="party-kpi-value" style="font-size: 15px;">
                مدين: {{ $money($totals['period_debit_total'] ?? 0) }}
                <br>
                دائن: {{ $money($totals['period_credit_total'] ?? 0) }}
            </div>
        </div>
    </div>

    <div class="party-two-grid">
        <div class="party-card">
            <div class="party-table-header">
                <h3 class="party-table-title">أعلى أرصدة مدينة</h3>
                <div class="party-subtitle">أعلى 10 {{ $partyPluralLabel }} عليهم رصيد مدين.</div>
            </div>

            <div class="party-table-wrapper">
                <table class="party-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>الكود</th>
                        <th>{{ $partyLabel }}</th>
                        <th class="text-left">الرصيد</th>
                        <th class="text-left">الحركات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['top_debit'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td><span class="party-badge">{{ $row['code'] }}</span></td>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-left party-positive">{{ $row['closing_balance_label'] }}</td>
                            <td class="text-left">{{ $row['rows_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="party-empty">لا توجد أرصدة مدينة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="party-card">
            <div class="party-table-header">
                <h3 class="party-table-title">أعلى أرصدة دائنة</h3>
                <div class="party-subtitle">أعلى 10 {{ $partyPluralLabel }} لهم رصيد دائن.</div>
            </div>

            <div class="party-table-wrapper">
                <table class="party-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>الكود</th>
                        <th>{{ $partyLabel }}</th>
                        <th class="text-left">الرصيد</th>
                        <th class="text-left">الحركات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['top_credit'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td><span class="party-badge">{{ $row['code'] }}</span></td>
                            <td>{{ $row['name'] }}</td>
                            <td class="text-left party-negative">{{ $row['closing_balance_label'] }}</td>
                            <td class="text-left">{{ $row['rows_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="party-empty">لا توجد أرصدة دائنة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="party-card">
        <div class="party-table-header">
            <h3 class="party-table-title">كل أرصدة {{ $partyPluralLabel }}</h3>
            <div class="party-subtitle">مرتبة حسب أعلى قيمة مطلقة للرصيد.</div>
        </div>

        <div class="party-table-wrapper">
            <table class="party-table">
                <thead>
                <tr>
                    <th style="width:45px;text-align:center;">م</th>
                    <th>الكود</th>
                    <th>{{ $partyLabel }}</th>
                    <th>الفرع</th>
                    <th>تليفون</th>
                    <th class="text-left">افتتاحي</th>
                    <th class="text-left">مدين الفترة</th>
                    <th class="text-left">دائن الفترة</th>
                    <th class="text-left">الرصيد</th>
                    <th class="text-left">كشف الحساب</th>
                </tr>
                </thead>
                <tbody>
                @forelse($report['rows'] ?? [] as $row)
                    <tr>
                        <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                        <td><span class="party-badge">{{ $row['code'] }}</span></td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['branch_name'] }}</td>
                        <td>{{ $row['phone'] }}</td>
                        <td class="text-left">{{ $money(abs((float) $row['opening_balance'])) }}</td>
                        <td class="text-left party-positive">{{ $money($row['period_debit']) }}</td>
                        <td class="text-left party-negative">{{ $money($row['period_credit']) }}</td>
                        <td class="text-left {{ $balanceClass($row['balance_side']) }}">
                            {{ $row['closing_balance_label'] }}
                        </td>
                        <td class="text-left">
                            <a href="{{ $row['ledger_url'] }}" target="_blank" class="party-link">
                                عرض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="party-empty">لا توجد بيانات أرصدة.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>