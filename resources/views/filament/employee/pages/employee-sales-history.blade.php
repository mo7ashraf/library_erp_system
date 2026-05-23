<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    @endphp

    <x-erp.report-page-styles />

    <div class="erp-report-page">
        <div class="erp-report-card">
            <div class="erp-report-header">
                <div>
                    <h2 class="erp-report-title">مبيعاتي</h2>
                    <div class="erp-report-subtitle">
                        عرض فواتير البيع التي قمت بإنشائها مع إمكانية إعادة الطباعة.
                    </div>
                </div>

                <a href="/employee/sales-desk" class="erp-report-btn erp-report-btn-primary">
                    نقطة البيع
                </a>
            </div>
        </div>

        <div class="erp-report-card">
            <div class="erp-report-card-body">
                <div class="erp-report-filter-grid">
                    <div class="erp-report-field">
                        <label>من تاريخ</label>
                        <input type="date" wire:model.live="fromDate">
                    </div>

                    <div class="erp-report-field">
                        <label>إلى تاريخ</label>
                        <input type="date" wire:model.live="toDate">
                    </div>

                    <div class="erp-report-field">
                        <label>&nbsp;</label>
                        <button type="button" class="erp-report-btn erp-report-btn-primary" wire:click="loadSales">
                            تحديث
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="erp-report-kpi-grid">
            <div class="erp-report-kpi-card">
                <div class="erp-report-kpi-label">عدد الفواتير</div>
                <div class="erp-report-kpi-value">{{ $invoicesCount }}</div>
            </div>

            <div class="erp-report-kpi-card">
                <div class="erp-report-kpi-label">إجمالي المبيعات</div>
                <div class="erp-report-kpi-value">{{ $money($totalSales) }}</div>
            </div>
        </div>

        <div class="erp-report-card">
            <div class="erp-report-table-header">
                <h3 class="erp-report-table-title">فواتير البيع</h3>
            </div>

            <div class="erp-report-table-wrapper">
                <table class="erp-report-table">
                    <thead>
                    <tr>
                        <th style="width: 45px;">م</th>
                        <th>التاريخ</th>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>المخزن</th>
                        <th>طريقة الدفع</th>
                        <th>الإجمالي</th>
                        <th>تاريخ الترحيل</th>
                        <th>طباعة</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row['invoice_date'] }}</td>
                            <td>{{ $row['invoice_number'] }}</td>
                            <td>{{ $row['customer'] }}</td>
                            <td>{{ $row['warehouse'] }}</td>
                            <td>{{ $row['payment_type'] }}</td>
                            <td>{{ $money($row['grand_total']) }}</td>
                            <td>{{ $row['posted_at'] }}</td>
                            <td>
                                <a
                                    href="{{ route('admin.prints.sales-invoices.receipt', $row['id']) }}"
                                    target="_blank"
                                    class="erp-report-btn erp-report-btn-secondary"
                                >
                                    طباعة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 24px; color: #6b7280; font-weight: 800;">
                                لا توجد فواتير بيع في الفترة المحددة.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>