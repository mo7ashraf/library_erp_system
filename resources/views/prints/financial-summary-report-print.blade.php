<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الملخص المالي</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <x-erp.print-page-styles orientation="landscape" />
</head>
<body>

@php
    $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';

    $totalInflow = (float) ($report['total_inflow'] ?? 0);
    $totalOutflow = (float) ($report['total_outflow'] ?? 0);
    $netMovement = $totalInflow - $totalOutflow;

    $valueClass = function (float $value): string {
        if ($value > 0) {
            return 'erp-print-positive';
        }

        if ($value < 0) {
            return 'erp-print-negative';
        }

        return 'erp-print-neutral';
    };
@endphp

<div class="erp-print-actions">
    <button class="erp-print-btn" onclick="window.print()">طباعة</button>
    <button class="erp-print-btn erp-print-btn-secondary" onclick="window.close()">إغلاق</button>
</div>

<div class="erp-print-page">
    <div class="erp-print-header">
        <h1>الملخص المالي</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="erp-print-info-grid">
        <div class="erp-print-box">
            <h3 class="erp-print-box-title">بيانات التقرير</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">نوع التقرير</span>
                <span class="erp-print-value">ملخص مالي</span>
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
            <div class="erp-print-summary-label">إجمالي الداخل</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($totalInflow) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي الخارج</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($totalOutflow) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">صافي الحركة</div>
            <div class="erp-print-summary-value {{ $valueClass($netMovement) }}">
                {{ $money($netMovement) }}
            </div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي الأرصدة الحالية</div>
            <div class="erp-print-summary-value erp-print-neutral">
                {{ $money(($report['cashbox_total_balance'] ?? 0) + ($report['bank_total_balance'] ?? 0)) }}
            </div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">داخل الخزائن</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($report['cash_inflow'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">خارج الخزائن</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($report['cash_outflow'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">داخل البنوك</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $money($report['bank_inflow'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">خارج البنوك</div>
            <div class="erp-print-summary-value erp-print-negative">{{ $money($report['bank_outflow'] ?? 0) }}</div>
        </div>
    </div>

    <h2 class="erp-print-section-title">ملخص أنواع الحركات</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع الحركة</th>
            <th>الاتجاه</th>
            <th class="erp-print-text-left">عدد الحركات</th>
            <th class="erp-print-text-left">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['transaction_type_summary'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['transaction_type_label'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'erp-print-positive' : 'erp-print-negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="erp-print-text-left">{{ $row['transactions_count'] }}</td>
                <td class="erp-print-text-left {{ $row['direction'] === 'in' ? 'erp-print-positive' : 'erp-print-negative' }}">
                    {{ $money($row['total_amount']) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات في الفترة المحددة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أرصدة الخزائن</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الخزينة</th>
            <th>الفرع</th>
            <th class="erp-print-text-left">داخل الفترة</th>
            <th class="erp-print-text-left">خارج الفترة</th>
            <th class="erp-print-text-left">الرصيد الحالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['cashboxes'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['branch_name'] }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['period_in']) }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['period_out']) }}</td>
                <td class="erp-print-text-left erp-print-neutral">{{ $money($row['current_balance']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد خزائن.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">أرصدة البنوك</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الحساب</th>
            <th>البنك</th>
            <th class="erp-print-text-left">داخل الفترة</th>
            <th class="erp-print-text-left">خارج الفترة</th>
            <th class="erp-print-text-left">الرصيد الحالي</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['bank_accounts'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td>{{ $row['bank_name'] }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $money($row['period_in']) }}</td>
                <td class="erp-print-text-left erp-print-negative">{{ $money($row['period_out']) }}</td>
                <td class="erp-print-text-left erp-print-neutral">{{ $money($row['current_balance']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حسابات بنكية.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">آخر الحركات المالية</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>التاريخ</th>
            <th>رقم الحركة</th>
            <th>النوع</th>
            <th>الحساب</th>
            <th>الاتجاه</th>
            <th class="erp-print-text-left">المبلغ</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['latest_transactions'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['transaction_date'] }}</td>
                <td>{{ $row['transaction_number'] }}</td>
                <td><span class="erp-print-badge">{{ $row['transaction_type_label'] }}</span></td>
                <td>{{ $row['account_name'] }}</td>
                <td>
                    <span class="{{ $row['direction'] === 'in' ? 'erp-print-positive' : 'erp-print-negative' }}">
                        {{ $row['direction_label'] }}
                    </span>
                </td>
                <td class="erp-print-text-left {{ $row['direction'] === 'in' ? 'erp-print-positive' : 'erp-print-negative' }}">
                    {{ $money($row['amount']) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد حركات مالية في الفترة المحددة.
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