<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
        $rowsCount = count($ledger['rows'] ?? []);
        $perPage = (int) request()->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $currentPage = max(1, (int) request()->query('page', 1));
        $totalPages = max(1, (int) ceil($rowsCount / $perPage));

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $startIndex = ($currentPage - 1) * $perPage;
        $visibleRows = array_slice($ledger['rows'] ?? [], $startIndex, $perPage);
        $closingBalance = (float) ($ledger['closing_balance'] ?? 0);
        $openingBalance = (float) ($ledger['opening_balance'] ?? 0);

        $balanceClass = function (float $value): string {
            if ($value > 0) {
                return 'ledger-balance-debit';
            }

            if ($value < 0) {
                return 'ledger-balance-credit';
            }

            return 'ledger-balance-zero';
        };
    @endphp

    <style>
        .ledger-page {
            direction: rtl;
            font-family: "Cairo", Tahoma, Arial, sans-serif;
            color: #111827;
        }

        .ledger-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .ledger-card-body {
            padding: 18px;
        }

        .ledger-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px;
        }

        .ledger-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.4;
        }

        .ledger-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }

        .ledger-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            padding: 7px 13px;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .ledger-filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .ledger-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .ledger-field select,
        .ledger-field input {
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

        .ledger-field select:focus,
        .ledger-field input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
        }

        .ledger-actions {
            display: flex;
            gap: 8px;
        }

        .ledger-btn {
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

        .ledger-btn-primary {
            background: #f59e0b;
            color: #111827;
        }

        .ledger-btn-secondary {
            background: #6b7280;
            color: #ffffff;
        }

        .ledger-info-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 16px;
        }

        .ledger-party-name {
            font-size: 26px;
            font-weight: 900;
            color: #111827;
            margin-top: 6px;
        }

        .ledger-party-code {
            display: inline-flex;
            margin-top: 8px;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 800;
        }

        .ledger-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 800;
        }

        .ledger-value {
            margin-top: 5px;
            font-size: 17px;
            font-weight: 900;
            color: #111827;
        }

        .ledger-period-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .ledger-mini-box {
            background: #f9fafb;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 14px;
        }

        .ledger-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .ledger-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .ledger-kpi-title {
            font-size: 13px;
            font-weight: 900;
            color: #6b7280;
        }

        .ledger-kpi-value {
            margin-top: 10px;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.3;
        }

        .ledger-kpi-debit {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .ledger-kpi-debit .ledger-kpi-title,
        .ledger-kpi-debit .ledger-kpi-value {
            color: #15803d;
        }

        .ledger-kpi-credit {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .ledger-kpi-credit .ledger-kpi-title,
        .ledger-kpi-credit .ledger-kpi-value {
            color: #b91c1c;
        }

        .ledger-kpi-final {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .ledger-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .ledger-table-title {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
        }

        .ledger-table-wrapper {
            overflow-x: auto;
        }

        .ledger-table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .ledger-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
            text-align: right;
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .ledger-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .ledger-table tbody tr:hover {
            background: #fffbeb;
        }

        .ledger-table .text-left {
            text-align: left;
        }

        .ledger-doc-badge {
            display: inline-flex;
            background: #f3f4f6;
            color: #374151;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .ledger-reference {
            font-family: Consolas, monospace;
            font-size: 13px;
            font-weight: 800;
            color: #374151;
            direction: ltr;
            text-align: right;
        }

        .ledger-debit {
            color: #15803d;
            font-weight: 900;
            white-space: nowrap;
        }

        .ledger-credit {
            color: #b91c1c;
            font-weight: 900;
            white-space: nowrap;
        }

        .ledger-balance-debit {
            color: #b45309;
            font-weight: 900;
            white-space: nowrap;
        }

        .ledger-balance-credit {
            color: #1d4ed8;
            font-weight: 900;
            white-space: nowrap;
        }

        .ledger-balance-zero {
            color: #374151;
            font-weight: 900;
            white-space: nowrap;
        }

        .ledger-opening-row {
            background: #fff7ed;
        }

        .ledger-empty {
            text-align: center;
            padding: 36px 12px;
            color: #6b7280;
            font-weight: 800;
        }

        .ledger-footer-row td {
            background: #111827;
            color: #ffffff;
            font-weight: 900;
            font-size: 15px;
            border-bottom: none;
        }

        @media (max-width: 1100px) {
            .ledger-filter-grid,
            .ledger-info-grid,
            .ledger-kpi-grid {
                grid-template-columns: 1fr;
            }

            .ledger-period-grid {
                grid-template-columns: 1fr;
            }

            .ledger-actions {
                width: 100%;
            }

            .ledger-btn {
                width: 100%;
            }
        }

        @media print {
            .fi-sidebar,
            .fi-topbar,
            .ledger-card:first-child {
                display: none !important;
            }

            .ledger-page {
                background: #ffffff;
            }

            .ledger-card,
            .ledger-kpi {
                box-shadow: none;
            }
        }
    </style>

    <div class="ledger-page">

        <div class="ledger-card">
            <div class="ledger-header">
                <div>
                    <h2 class="ledger-title">كشف حساب عميل</h2>
                    <div class="ledger-subtitle">
                        اختر العميل والفترة الزمنية لعرض تفاصيل الرصيد والحركات.
                    </div>
                </div>

                <div class="ledger-badge">
                    عدد الحركات: {{ $rowsCount }}
                </div>
            </div>

            <div class="ledger-card-body">
                <form method="GET">
                    <div class="ledger-filter-grid">
                        <div class="ledger-field">
                            <label>العميل</label>
                            <select name="customer_id">
                                @foreach($customers as $id => $name)
                                    <option value="{{ $id }}" @selected((int) $customerId === (int) $id)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ledger-field">
                            <label>من تاريخ</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}">
                        </div>

                        <div class="ledger-field">
                            <label>إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ $toDate }}">
                        </div>

                        <div class="ledger-actions">
                            <button type="submit" class="ledger-btn ledger-btn-primary">
                                عرض الكشف
                            </button>

                            <a
                                href="{{ route('admin.prints.customer-ledger', [
                                    'customer_id' => $customerId,
                                    'from_date' => $fromDate,
                                    'to_date' => $toDate,
                                ]) }}"
                                target="_blank"
                                class="ledger-btn ledger-btn-secondary"
                            >
                                طباعة
                            </a>

                            <a href="{{ url()->current() }}" class="ledger-btn ledger-btn-secondary">
                                مسح
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(! $customerId)
            <div class="ledger-card">
                <div class="ledger-card-body">
                    لا يوجد عملاء لعرض كشف الحساب.
                </div>
            </div>
        @else
            <div class="ledger-card">
                <div class="ledger-card-body">
                    <div class="ledger-info-grid">
                        <div>
                            <div class="ledger-label">بيانات العميل</div>
                            <div class="ledger-party-name">{{ $ledger['party_name'] ?? '-' }}</div>
                            <div class="ledger-party-code">كود العميل: {{ $ledger['party_code'] ?? '-' }}</div>
                        </div>

                        <div class="ledger-period-grid">
                            <div class="ledger-mini-box">
                                <div class="ledger-label">الفترة من</div>
                                <div class="ledger-value">{{ $ledger['from_date'] ?? 'بداية الحساب' }}</div>
                            </div>

                            <div class="ledger-mini-box">
                                <div class="ledger-label">الفترة إلى</div>
                                <div class="ledger-value">{{ $ledger['to_date'] ?? 'حتى الآن' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ledger-kpi-grid">
                <div class="ledger-kpi">
                    <div class="ledger-kpi-title">رصيد أول المدة</div>
                    <div class="ledger-kpi-value {{ $balanceClass($openingBalance) }}">
                        {{ $ledger['opening_balance_label'] }}
                    </div>
                </div>

                <div class="ledger-kpi ledger-kpi-debit">
                    <div class="ledger-kpi-title">إجمالي المدين</div>
                    <div class="ledger-kpi-value">{{ $money($ledger['total_debit']) }}</div>
                </div>

                <div class="ledger-kpi ledger-kpi-credit">
                    <div class="ledger-kpi-title">إجمالي الدائن</div>
                    <div class="ledger-kpi-value">{{ $money($ledger['total_credit']) }}</div>
                </div>

                <div class="ledger-kpi ledger-kpi-final">
                    <div class="ledger-kpi-title">الرصيد الختامي</div>
                    <div class="ledger-kpi-value {{ $balanceClass($closingBalance) }}">
                        {{ $ledger['closing_balance_label'] }}
                    </div>
                </div>
            </div>

            <div class="ledger-card">
                <div class="ledger-table-header">
                    <div>
                        <h3 class="ledger-table-title">تفاصيل الحركات</h3>
                        <div class="ledger-subtitle">ترتيب الحركات حسب التاريخ ونوع المستند.</div>
                    </div>

                    <div class="ledger-badge">{{ $rowsCount }} حركة</div>
                </div>

                <div class="ledger-table-wrapper">
                    <table class="ledger-table">
                        <thead>
                        <tr>
                            <th style="width: 56px; text-align: center;">م</th>
                            <th>التاريخ</th>
                            <th>نوع المستند</th>
                            <th>رقم المرجع</th>
                            <th>البيان</th>
                            <th class="text-left">مدين</th>
                            <th class="text-left">دائن</th>
                            <th class="text-left">الرصيد بعد الحركة</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr class="ledger-opening-row">
                            <td style="text-align: center; font-weight: 900;">0</td>
                            <td>{{ $ledger['from_date'] ?? '-' }}</td>
                            <td><span class="ledger-doc-badge">رصيد افتتاحي</span></td>
                            <td>-</td>
                            <td>رصيد أول المدة للفترة المحددة</td>
                            <td class="text-left">-</td>
                            <td class="text-left">-</td>
                            <td class="text-left {{ $balanceClass($openingBalance) }}">
                                {{ $ledger['opening_balance_label'] }}
                            </td>
                        </tr>

                        @forelse($visibleRows as $row)
                            @php
                                $debit = (float) $row['debit'];
                                $credit = (float) $row['credit'];
                                $balance = (float) $row['balance'];
                            @endphp

                            <tr>
                                <td style="text-align: center; font-weight: 900;">
                                    {{ $startIndex + $loop->iteration }}
                                </td>
                                <td>{{ $row['date'] }}</td>
                                <td><span class="ledger-doc-badge">{{ $row['document_type'] }}</span></td>
                                <td class="ledger-reference">{{ $row['reference_number'] }}</td>
                                <td>{{ $row['description'] }}</td>
                                <td class="text-left ledger-debit">{{ $debit > 0 ? $money($debit) : '-' }}</td>
                                <td class="text-left ledger-credit">{{ $credit > 0 ? $money($credit) : '-' }}</td>
                                <td class="text-left {{ $balanceClass($balance) }}">{{ $row['balance_label'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="ledger-empty">
                                    لا توجد حركات في الفترة المحددة.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>

                        <tfoot>
                        <tr class="ledger-footer-row">
                            <td colspan="5">الإجمالي</td>
                            <td class="text-left">{{ $money($ledger['total_debit']) }}</td>
                            <td class="text-left">{{ $money($ledger['total_credit']) }}</td>
                            <td class="text-left">{{ $ledger['closing_balance_label'] }}</td>
                        </tr>
                        </tfoot>
                    </table>
                    @if($rowsCount > 0)
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; border-top: 1px solid #f1f5f9; background: #ffffff;">
                            <div style="font-size: 13px; font-weight: 800; color: #6b7280;">
                                عرض {{ $startIndex + 1 }}
                                إلى {{ min($startIndex + $perPage, $rowsCount) }}
                                من {{ $rowsCount }} حركة
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <form method="GET" style="margin: 0;">
                                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach

                                    <select
                                        name="per_page"
                                        onchange="this.form.submit()"
                                        style="height: 36px; border: 1px solid #d1d5db; border-radius: 10px; padding: 0 10px; font-weight: 800;"
                                    >
                                        @foreach([10, 25, 50, 100] as $option)
                                            <option value="{{ $option }}" @selected($perPage === $option)>
                                                {{ $option }} / صفحة
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                @if($currentPage > 1)
                                    <a
                                        href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1, 'per_page' => $perPage]) }}"
                                        style="height: 36px; min-width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #f3f4f6; color: #111827; font-weight: 900; text-decoration: none;"
                                    >
                                        السابق
                                    </a>
                                @endif

                                <span style="height: 36px; min-width: 70px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #111827; color: #ffffff; font-weight: 900;">
                                    {{ $currentPage }} / {{ $totalPages }}
                                </span>

                                @if($currentPage < $totalPages)
                                    <a
                                        href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1, 'per_page' => $perPage]) }}"
                                        style="height: 36px; min-width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #f3f4f6; color: #111827; font-weight: 900; text-decoration: none;"
                                    >
                                        التالي
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>