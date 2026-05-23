<x-filament-panels::page>
    <x-erp.report-page-styles />

    <div class="erp-report-page">
        <div class="erp-report-card">
            <div class="erp-report-header">
                <div>
                    <h2 class="erp-report-title">طلبات معلقة</h2>
                    <div class="erp-report-subtitle">
                        الطلبات التي تم تعليقها من نقطة البيع ولم يتم إنهاؤها بعد.
                    </div>
                </div>

                <a href="/employee/sales-desk" class="erp-report-btn erp-report-btn-primary">
                    نقطة البيع
                </a>
            </div>
        </div>

        <div class="erp-report-card">
            <div class="erp-report-table-header">
                <h3 class="erp-report-table-title">قائمة الطلبات المعلقة</h3>
            </div>

            <div class="erp-report-table-wrapper">
                <table class="erp-report-table">
                    <thead>
                    <tr>
                        <th style="width: 45px;">م</th>
                        <th>اسم الطلب</th>
                        <th>اسم العميل</th>
                        <th>رقم الموبايل</th>
                        <th>عدد الأصناف</th>
                        <th>تاريخ التعليق</th>
                        <th>إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($heldOrders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $order['name'] ?? '-' }}</td>
                            <td>{{ $order['customer_name'] ?? '-' }}</td>
                            <td>{{ $order['customer_phone'] ?? '-' }}</td>
                            <td>{{ count($order['lines'] ?? []) }}</td>
                            <td>{{ $order['created_at'] ?? '-' }}</td>
                            <td>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <button
                                        type="button"
                                        class="erp-report-btn erp-report-btn-primary"
                                        wire:click="resume('{{ $order['id'] }}')"
                                    >
                                        استرجاع
                                    </button>

                                    <button
                                        type="button"
                                        class="erp-report-btn erp-report-btn-secondary"
                                        wire:click="delete('{{ $order['id'] }}')"
                                    >
                                        حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:24px; color:#6b7280; font-weight:800;">
                                لا توجد طلبات معلقة.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>