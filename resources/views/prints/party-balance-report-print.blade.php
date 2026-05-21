<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'تقرير الأرصدة' }}</title>

    <x-erp.print-page-styles orientation="landscape" />

    <style>
        .party-balance-wide-table .col-small {
            width: 30px;
        }

        .party-balance-wide-table .col-code {
            width: 70px;
        }

        .party-balance-wide-table .col-name {
            width: 150px;
        }

        .party-balance-wide-table .col-branch {
            width: 90px;
        }

        .party-balance-wide-table .col-phone {
            width: 90px;
        }

        .party-balance-wide-table .col-money {
            width: 85px;
        }

        .party-balance-wide-table .col-balance {
            width: 95px;
        }
    </style>
</head>
<body>

@php
    $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    $totals = $report['totals'] ?? [];

    $balanceClass = function (?string $side): string {
        return match ($side) {
            'debit' => 'erp-print-positive',
            'credit' => 'erp-print-negative',
            default => 'erp-print-neutral',
        };
    };
@endphp

<div class="erp-print-actions">
    <button class="erp-print-btn" onclick="window.print()">طباعة</button>
    <button class="erp-print-btn erp-print-btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="erp-print-page">
    <div class="erp-print-header">
        <h1>{{ $title ?? ($report['title'] ?? 'تقرير الأرصدة') }}</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="erp-print-info-grid">
        <div class="erp-print-box">
            <h3 class="erp-print-box-title">بيانات التقرير</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">نوع التقرير</span>
                <span class="erp-print-value">{{ $report['title'] ?? 'تقرير الأرصدة' }}</span>
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
            <div class="erp-print-summary-label">عدد {{ $partyPluralLabel }}</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $totals['parties_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">أرصدة مدينة</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($totals['closing_debit_total'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">أرصدة دائنة</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($totals['closing_credit_total'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">صافي الرصيد</div>
            <div class="erp-print-summary-value {{ ($totals['net_balance'] ?? 0) >= 0 ? 'erp-print-positive' : 'erp-print-negative' }}">
                {{ $money(abs((float) ($totals['net_balance'] ?? 0))) }}
                {{ ($totals['net_balance'] ?? 0) >= 0 ? 'مدين' : 'دائن' }}
            </div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">عدد الأرصدة المدينة</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $totals['debit_parties_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">عدد الأرصدة الدائنة</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $totals['credit_parties_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">أرصدة صفرية</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $totals['zero_parties_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">حركة الفترة</div>
            <div class="erp-print-summary-value erp-print-neutral" style="font-size: 12px;">
                مدين: {{ $money($totals['period_debit_total'] ?? 0) }}
                <br>
                دائن: {{ $money($totals['period_credit_total'] ?? 0) }}
            </div>
        </div>
    </div>

    <h2 class="erp-print-section-title">أعلى أرصدة مدينة</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width:35px;text-align:center;">م</th>
            <th>الكود</th>
            <th>{{ $partyLabel }}</th>
            <th class="erp-print-text-left">الرصيد</th>
            <th class="erp-print-text-left">الحركات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_debit'] ?? [] as $row)
            <tr>
                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['code'] }}</span></td>
                <td>{{ $row['name'] }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $row['closing_balance_label'] }}</td>
                <td class="erp-print-text-left">{{ $row['rows_count'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:20px;color:#6b7280;font-weight:800;">
                    لا توجد أرصدة مدينة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أعلى أرصدة دائنة</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width:35px;text-align:center;">م</th>
            <th>الكود</th>
            <th>{{ $partyLabel }}</th>
            <th class="erp-print-text-left">الرصيد</th>
            <th class="erp-print-text-left">الحركات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['top_credit'] ?? [] as $row)
            <tr>
                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['code'] }}</span></td>
                <td>{{ $row['name'] }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $row['closing_balance_label'] }}</td>
                <td class="erp-print-text-left">{{ $row['rows_count'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:20px;color:#6b7280;font-weight:800;">
                    لا توجد أرصدة دائنة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">كل أرصدة {{ $partyPluralLabel }}</h2>

    <table class="erp-print-table erp-print-compact-table party-balance-wide-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-code">الكود</th>
            <th class="col-name">{{ $partyLabel }}</th>
            <th class="col-branch">الفرع</th>
            <th class="col-phone">تليفون</th>
            <th class="col-money erp-print-text-left">افتتاحي</th>
            <th class="col-money erp-print-text-left">مدين الفترة</th>
            <th class="col-money erp-print-text-left">دائن الفترة</th>
            <th class="col-balance erp-print-text-left">الرصيد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['rows'] ?? [] as $row)
            <tr>
                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                <td><span class="erp-print-badge">{{ $row['code'] }}</span></td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['branch_name'] }}</td>
                <td>{{ $row['phone'] }}</td>
                <td class="erp-print-text-left">{{ $money(abs((float) $row['opening_balance'])) }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['period_debit']) }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['period_credit']) }}</td>
                <td class="erp-print-text-left {{ $balanceClass($row['balance_side']) }}">
                    {{ $row['closing_balance_label'] }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:20px;color:#6b7280;font-weight:800;">
                    لا توجد بيانات أرصدة.
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