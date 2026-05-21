<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مراجعة المستندات</title>

    <x-erp.print-page-styles orientation="landscape" />

    <style>
        .posted-documents-wide-table .col-small {
            width: 30px;
        }

        .posted-documents-wide-table .col-date {
            width: 62px;
        }

        .posted-documents-wide-table .col-type {
            width: 95px;
        }

        .posted-documents-wide-table .col-number {
            width: 85px;
        }

        .posted-documents-wide-table .col-party {
            width: 135px;
        }

        .posted-documents-wide-table .col-branch {
            width: 80px;
        }

        .posted-documents-wide-table .col-user {
            width: 80px;
        }

        .posted-documents-wide-table .col-status {
            width: 58px;
        }

        .posted-documents-wide-table .col-posted {
            width: 92px;
        }

        .posted-documents-wide-table .col-qty {
            width: 65px;
        }

        .posted-documents-wide-table .col-money {
            width: 78px;
        }
    </style>
</head>
<body>

@php
    $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    $number = fn ($value) => number_format((float) $value, 3);
    $totals = $report['totals'] ?? [];

    $statusLabel = function (?string $state): string {
        return match ($state) {
            'posted' => 'مرحّل',
            'draft' => 'مسودة',
            'all' => 'الكل',
            default => $state ?: '-',
        };
    };

    $statusClass = function (?string $state): string {
        return match ($state) {
            'posted' => 'erp-print-positive',
            'draft' => 'erp-print-warning',
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
        <h1>تقرير مراجعة المستندات</h1>
        <div class="subtitle">نظام إدارة المكتبة</div>
    </div>

    <div class="erp-print-info-grid">
        <div class="erp-print-box">
            <h3 class="erp-print-box-title">بيانات التقرير</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">نوع التقرير</span>
                <span class="erp-print-value">مراجعة المستندات التشغيلية</span>
            </div>

            <div class="erp-print-line">
                <span class="erp-print-label">تاريخ الطباعة</span>
                <span class="erp-print-value">{{ now()->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="erp-print-box">
            <h3 class="erp-print-box-title">الفترة والحالة</h3>

            <div class="erp-print-line">
                <span class="erp-print-label">من تاريخ</span>
                <span class="erp-print-value">{{ $fromDate ?? 'بداية النظام' }}</span>
            </div>

            <div class="erp-print-line">
                <span class="erp-print-label">إلى تاريخ</span>
                <span class="erp-print-value">{{ $toDate ?? 'حتى الآن' }}</span>
            </div>

            <div class="erp-print-line">
                <span class="erp-print-label">الحالة</span>
                <span class="erp-print-value">{{ $statusLabel($status ?? 'all') }}</span>
            </div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">عدد المستندات</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $totals['documents_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مستندات مرحلة</div>
            <div class="erp-print-summary-value erp-print-positive">{{ $totals['posted_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">مستندات مسودة</div>
            <div class="erp-print-summary-value erp-print-warning">{{ $totals['draft_count'] ?? 0 }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي القيم</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $money($totals['total_amount'] ?? 0) }}</div>
        </div>
    </div>

    <div class="erp-print-summary-grid">
        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إجمالي الكميات</div>
            <div class="erp-print-summary-value erp-print-neutral">{{ $number($totals['total_quantity'] ?? 0) }}</div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">من تاريخ</div>
            <div class="erp-print-summary-value erp-print-neutral" style="font-size: 12px;">
                {{ $fromDate ?? '-' }}
            </div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">إلى تاريخ</div>
            <div class="erp-print-summary-value erp-print-neutral" style="font-size: 12px;">
                {{ $toDate ?? '-' }}
            </div>
        </div>

        <div class="erp-print-summary-card">
            <div class="erp-print-summary-label">فلتر الحالة</div>
            <div class="erp-print-summary-value {{ $statusClass($status ?? 'all') }}">
                {{ $statusLabel($status ?? 'all') }}
            </div>
        </div>
    </div>

    <h2 class="erp-print-section-title">ملخص حسب نوع المستند</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>نوع المستند</th>
            <th class="erp-print-text-left">العدد</th>
            <th class="erp-print-text-left">مرحّل</th>
            <th class="erp-print-text-left">مسودة</th>
            <th class="erp-print-text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['by_document_type'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['document_type_label'] }}</td>
                <td class="erp-print-text-left">{{ $row['documents_count'] }}</td>
                <td class="erp-print-text-left erp-print-positive">{{ $row['posted_count'] }}</td>
                <td class="erp-print-text-left erp-print-warning">{{ $row['draft_count'] }}</td>
                <td class="erp-print-text-left">{{ $money($row['total_amount']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">ملخص حسب الحالة</h2>

    <table class="erp-print-table">
        <thead>
        <tr>
            <th style="width: 35px; text-align:center;">م</th>
            <th>الحالة</th>
            <th class="erp-print-text-left">العدد</th>
            <th class="erp-print-text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['by_status'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>
                    <span class="{{ $statusClass($row['status'] ?? null) }}">
                        {{ $row['status_label'] }}
                    </span>
                </td>
                <td class="erp-print-text-left">{{ $row['documents_count'] }}</td>
                <td class="erp-print-text-left">{{ $money($row['total_amount']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد بيانات.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h2 class="erp-print-section-title">تفاصيل المستندات</h2>

    <table class="erp-print-table erp-print-compact-table posted-documents-wide-table">
        <thead>
        <tr>
            <th class="col-small">م</th>
            <th class="col-date">التاريخ</th>
            <th class="col-type">نوع المستند</th>
            <th class="col-number">رقم المستند</th>
            <th class="col-party">الطرف / المخزن</th>
            <th class="col-branch">الفرع</th>
            <th class="col-user">المستخدم</th>
            <th class="col-status">الحالة</th>
            <th class="col-posted">تاريخ الترحيل</th>
            <th class="col-qty erp-print-text-left">الكمية</th>
            <th class="col-money erp-print-text-left">القيمة</th>
        </tr>
        </thead>
        <tbody>
        @forelse($report['rows'] ?? [] as $row)
            <tr>
                <td style="text-align:center; font-weight:900;">{{ $loop->iteration }}</td>
                <td>{{ $row['document_date'] }}</td>
                <td><span class="erp-print-badge">{{ $row['document_type_label'] }}</span></td>
                <td>{{ $row['document_number'] }}</td>
                <td>{{ $row['party'] }}</td>
                <td>{{ $row['branch'] }}</td>
                <td>{{ $row['user'] }}</td>
                <td>
                    <span class="{{ $statusClass($row['status'] ?? null) }}">
                        {{ $row['status_label'] }}
                    </span>
                </td>
                <td>{{ $row['posted_at'] }}</td>
                <td class="erp-print-text-left">
                    {{ $row['quantity'] === null ? '-' : $number($row['quantity']) }}
                </td>
                <td class="erp-print-text-left">{{ $money($row['amount']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="text-align:center; padding:20px; color:#6b7280; font-weight:800;">
                    لا توجد مستندات خلال الفترة المحددة.
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