<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
    @endphp

    <x-erp.report-page-styles />

    <div class="erp-report-page">
        <div class="erp-report-card">
            <div class="erp-report-header">
                <div>
                    <h2 class="erp-report-title">تقرير مبيعات المستخدمين</h2>
                    <div class="erp-report-subtitle">
                        متابعة مبيعات كل موظف خلال فترة محددة مع إمكانية مراجعة الفواتير وطباعتها.
                    </div>
                </div>
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
                        <label>الموظف</label>
                        <select wire:model.live="employeeId">
                            <option value="">كل الموظفين</option>
                            @foreach($employees as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="erp-report-field">
                        <label>&nbsp;</label>
                        <button type="button" class="erp-report-btn erp-report-btn-primary" wire:click="loadReport">
                            تحديث التقرير
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

            <div class="erp-report-kpi-card">
                <div class="erp-report-kpi-label">متوسط الفاتورة</div>
                <div class="erp-report-kpi-value">{{ $money($averageInvoiceValue) }}</div>
            </div>
        </div>

        <div class="erp-report-card">
            <div class="erp-report-table-header">
                <h3 class="erp-report-table-title">ملخص المبيعات حسب المستخدم</h3>
            </div>

            <div class="erp-report-table-wrapper">
                <table class="erp-report-table">
                    <thead>
                    <tr>
                        <th style="width: 45px;">م</th>
                        <th>المستخدم</th>
                        <th>عدد الفواتير</th>
                        <th>إجمالي المبيعات</th>
                        <th>متوسط الفاتورة</th>
                        <th>دفع كامل</th>
                        <th>دفع جزئي</th>
                        <th>آجل</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($summaryRows as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row['employee_name'] }}</td>
                            <td>{{ $row['invoices_count'] }}</td>
                            <td>{{ $money($row['total_sales']) }}</td>
                            <td>{{ $money($row['average_invoice_value']) }}</td>
                            <td>{{ $money($row['cash_total']) }}</td>
                            <td>{{ $money($row['partial_total']) }}</td>
                            <td>{{ $money($row['credit_total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 24px; color: #6b7280; font-weight: 800;">
                                لا توجد مبيعات في الفترة المحددة.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="erp-report-card">
            <div class="erp-report-table-header">
                <h3 class="erp-report-table-title">تفاصيل فواتير البيع</h3>
            </div>

            <div class="erp-report-table-wrapper">
                <table class="erp-report-table">
                    <thead>
                    <tr>
                        <th style="width: 45px;">م</th>
                        <th>التاريخ</th>
                        <th>رقم الفاتورة</th>
                        <th>المستخدم</th>
                        <th>العميل</th>
                        <th>المخزن</th>
                        <th>طريقة الدفع</th>
                        <th>قبل الخصم</th>
                        <th>الخصم</th>
                        <th>الإجمالي</th>
                        <th>طباعة</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($invoiceRows as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row['invoice_date'] }}</td>
                            <td>{{ $row['invoice_number'] }}</td>
                            <td>{{ $row['employee_name'] }}</td>
                            <td>{{ $row['customer_name'] }}</td>
                            <td>{{ $row['warehouse_name'] }}</td>
                            <td>{{ $row['payment_type'] }}</td>
                            <td>{{ $money($row['subtotal']) }}</td>
                            <td>{{ $money($row['discount_amount']) }}</td>
                            <td>{{ $money($row['grand_total']) }}</td>
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
                            <td colspan="11" style="text-align: center; padding: 24px; color: #6b7280; font-weight: 800;">
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