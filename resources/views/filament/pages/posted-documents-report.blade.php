<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
        $number = fn ($value) => number_format((float) $value, 3);
        $totals = $report['totals'] ?? [];

        $statusOptions = [
            'all' => 'الكل',
            'posted' => 'مرحّل',
            'draft' => 'مسودة',
        ];

        $statusClass = function (?string $status): string {
            return match ($status) {
                'posted' => 'audit-positive',
                'draft' => 'audit-warning',
                default => 'audit-neutral',
            };
        };
    @endphp

    <style>
        .audit-page {
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
        }

        .audit-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .audit-card-body {
            padding: 18px;
        }

        .audit-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 18px;
        }

        .audit-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.4;
        }

        .audit-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }

        .audit-filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .audit-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .audit-field input,
        .audit-field select {
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

        .audit-field input:focus,
        .audit-field select:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
        }

        .audit-actions {
            display: flex;
            gap: 8px;
        }

        .audit-btn {
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

        .audit-btn-primary {
            background: #f59e0b;
            color: #111827;
        }

        .audit-btn-secondary {
            background: #6b7280;
            color: #ffffff;
        }

        .audit-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .audit-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .audit-kpi-title {
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .audit-kpi-value {
            margin-top: 10px;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.3;
            color: #111827;
        }

        .audit-positive {
            color: #15803d;
            font-weight: 900;
        }

        .audit-negative {
            color: #b91c1c;
            font-weight: 900;
        }

        .audit-warning {
            color: #c2410c;
            font-weight: 900;
        }

        .audit-neutral {
            color: #374151;
            font-weight: 900;
        }

        .audit-two-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .audit-table-header {
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .audit-table-title {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
            color: #111827;
        }

        .audit-table-wrapper {
            overflow-x: auto;
        }

        .audit-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .audit-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .audit-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .audit-table tbody tr:hover {
            background: #fffbeb;
        }

        .audit-table .text-left {
            text-align: left;
        }

        .audit-badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .audit-empty {
            text-align: center;
            padding: 28px 12px;
            color: #6b7280;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .audit-filter-grid,
            .audit-kpi-grid,
            .audit-two-grid {
                grid-template-columns: 1fr;
            }

            .audit-actions,
            .audit-btn {
                width: 100%;
            }
        }
    </style>

    <div class="audit-page">
        <div class="audit-card">
            <div class="audit-header">
                <h2 class="audit-title">مراجعة المستندات</h2>
                <div class="audit-subtitle">
                    تقرير رقابي لكل المستندات خلال الفترة حسب النوع، الحالة، المستخدم، وتاريخ الترحيل.
                </div>
            </div>

            <div class="audit-card-body">
                <form method="GET">
                    <div class="audit-filter-grid">
                        <div class="audit-field">
                            <label>من تاريخ</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}">
                        </div>

                        <div class="audit-field">
                            <label>إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ $toDate }}">
                        </div>

                        <div class="audit-field">
                            <label>الحالة</label>
                            <select name="status">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($status === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="audit-actions">
                            <button type="submit" class="audit-btn audit-btn-primary">عرض التقرير</button>

                            <a
                                href="{{ route('admin.prints.posted-documents-report', [
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                    'status' => $status,
                                ]) }}"
                                target="_blank"
                                class="audit-btn audit-btn-secondary"
                            >
                                طباعة
                            </a>

                            <a href="{{ url()->current() }}" class="audit-btn audit-btn-secondary">مسح</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="audit-kpi-grid">
            <div class="audit-kpi">
                <div class="audit-kpi-title">عدد المستندات</div>
                <div class="audit-kpi-value">{{ $totals['documents_count'] ?? 0 }}</div>
            </div>

            <div class="audit-kpi">
                <div class="audit-kpi-title">مستندات مرحلة</div>
                <div class="audit-kpi-value audit-positive">{{ $totals['posted_count'] ?? 0 }}</div>
            </div>

            <div class="audit-kpi">
                <div class="audit-kpi-title">مستندات مسودة</div>
                <div class="audit-kpi-value audit-warning">{{ $totals['draft_count'] ?? 0 }}</div>
            </div>

            <div class="audit-kpi">
                <div class="audit-kpi-title">إجمالي القيم</div>
                <div class="audit-kpi-value">{{ $money($totals['total_amount'] ?? 0) }}</div>
            </div>
        </div>

        <div class="audit-two-grid">
            <div class="audit-card">
                <div class="audit-table-header">
                    <h3 class="audit-table-title">ملخص حسب نوع المستند</h3>
                    <div class="audit-subtitle">عدد المستندات وقيمتها لكل نوع.</div>
                </div>

                <div class="audit-table-wrapper">
                    <table class="audit-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>نوع المستند</th>
                            <th class="text-left">العدد</th>
                            <th class="text-left">مرحّل</th>
                            <th class="text-left">مسودة</th>
                            <th class="text-left">القيمة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['by_document_type'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>{{ $row['document_type_label'] }}</td>
                                <td class="text-left">{{ $row['documents_count'] }}</td>
                                <td class="text-left audit-positive">{{ $row['posted_count'] }}</td>
                                <td class="text-left audit-warning">{{ $row['draft_count'] }}</td>
                                <td class="text-left">{{ $money($row['total_amount']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="audit-empty">لا توجد بيانات.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="audit-card">
                <div class="audit-table-header">
                    <h3 class="audit-table-title">ملخص حسب الحالة</h3>
                    <div class="audit-subtitle">تجميع المستندات حسب الحالة.</div>
                </div>

                <div class="audit-table-wrapper">
                    <table class="audit-table">
                        <thead>
                        <tr>
                            <th style="width:45px;text-align:center;">م</th>
                            <th>الحالة</th>
                            <th class="text-left">العدد</th>
                            <th class="text-left">القيمة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['by_status'] ?? [] as $row)
                            <tr>
                                <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="{{ $statusClass($row['status']) }}">
                                        {{ $row['status_label'] }}
                                    </span>
                                </td>
                                <td class="text-left">{{ $row['documents_count'] }}</td>
                                <td class="text-left">{{ $money($row['total_amount']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="audit-empty">لا توجد بيانات.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="audit-card">
            <div class="audit-table-header">
                <h3 class="audit-table-title">تفاصيل المستندات</h3>
                <div class="audit-subtitle">كل المستندات خلال الفترة المحددة.</div>
            </div>

            <div class="audit-table-wrapper">
                <table class="audit-table">
                    <thead>
                    <tr>
                        <th style="width:45px;text-align:center;">م</th>
                        <th>التاريخ</th>
                        <th>نوع المستند</th>
                        <th>رقم المستند</th>
                        <th>الطرف / المخزن</th>
                        <th>الفرع</th>
                        <th>المستخدم</th>
                        <th>الحالة</th>
                        <th>تاريخ الترحيل</th>
                        <th class="text-left">الكمية</th>
                        <th class="text-left">القيمة</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($report['rows'] ?? [] as $row)
                        <tr>
                            <td style="text-align:center;font-weight:900;">{{ $loop->iteration }}</td>
                            <td>{{ $row['document_date'] }}</td>
                            <td><span class="audit-badge">{{ $row['document_type_label'] }}</span></td>
                            <td>{{ $row['document_number'] }}</td>
                            <td>{{ $row['party'] }}</td>
                            <td>{{ $row['branch'] }}</td>
                            <td>{{ $row['user'] }}</td>
                            <td>
                                <span class="{{ $statusClass($row['status']) }}">
                                    {{ $row['status_label'] }}
                                </span>
                            </td>
                            <td>{{ $row['posted_at'] }}</td>
                            <td class="text-left">{{ $row['quantity'] === null ? '-' : $number($row['quantity']) }}</td>
                            <td class="text-left">{{ $money($row['amount']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="audit-empty">لا توجد مستندات خلال الفترة المحددة.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>