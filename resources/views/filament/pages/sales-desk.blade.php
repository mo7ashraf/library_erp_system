<x-filament-panels::page>
    @php
        $money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
        $number = fn ($value) => number_format((float) $value, 3);
    @endphp

    <x-erp.report-page-styles />

    <style>
        .sales-desk-grid {
            display: grid;
            grid-template-columns: 1.7fr 0.8fr;
            gap: 18px;
        }

        .sales-desk-line-grid {
            display: grid;
            grid-template-columns: 2fr 0.8fr 0.8fr 0.8fr 0.8fr 0.5fr;
            gap: 10px;
            align-items: end;
            margin-bottom: 10px;
        }

        .sales-desk-total-box {
            background: #111827;
            color: #ffffff;
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 14px;
        }

        .sales-desk-total-label {
            font-size: 13px;
            color: #d1d5db;
            font-weight: 800;
        }

        .sales-desk-total-value {
            font-size: 30px;
            font-weight: 900;
            margin-top: 6px;
        }

        .sales-desk-side-line {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 0;
            font-weight: 800;
        }

        .sales-desk-remove-btn {
            height: 42px;
            border: none;
            border-radius: 12px;
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 900;
            cursor: pointer;
        }

        @media (max-width: 1200px) {
            .sales-desk-grid,
            .sales-desk-line-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="erp-report-page">
        <div class="erp-report-card">
            <div class="erp-report-header">
                <div>
                    <h2 class="erp-report-title">نقطة البيع</h2>
                    <div class="erp-report-subtitle">
                        شاشة مبسطة لموظف المكتبة لإنشاء فاتورة بيع وتحصيل المبلغ مباشرة.
                    </div>
                </div>

                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    @if($lastInvoiceId)
                        <a
                            href="{{ route('admin.prints.sales-invoices.receipt', $lastInvoiceId) }}"
                            target="_blank"
                            class="erp-report-btn erp-report-btn-secondary"
                        >
                            طباعة آخر فاتورة {{ $lastInvoiceNumber }}
                        </a>
                    @endif

                    <button type="button" class="erp-report-btn erp-report-btn-primary" wire:click="submitSale">
                        إنهاء البيع
                    </button>
                </div>
            </div>
        </div>

        <div class="sales-desk-grid">
            <div>
                <div class="erp-report-card">
                    <div class="erp-report-card-body">
                        <div class="erp-report-filter-grid">
                            <div class="erp-report-field">
                                <label>العميل</label>
                                <select wire:model.live="customerId">
                                    @foreach($customers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="erp-report-field">
                                <label>المخزن</label>
                                <select wire:model.live="warehouseId">
                                    @foreach($warehouses as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="erp-report-field">
                                <label>نوع السعر</label>
                                <select wire:model.live="priceType">
                                    <option value="student">سعر طالب</option>
                                    <option value="teacher">سعر مدرس</option>
                                    <option value="representative">سعر مندوب</option>
                                    <option value="retail">سعر قطاعي</option>
                                    <option value="wholesale">سعر جملة</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="erp-report-card">
                    <div class="erp-report-table-header">
                        <h3 class="erp-report-table-title">الأصناف</h3>
                    </div>

                    <div class="erp-report-card-body">
                        @foreach($lines as $index => $line)
                            <div class="sales-desk-line-grid" wire:key="sales-line-{{ $index }}">
                                <div class="erp-report-field">
                                    <label>الصنف</label>
                                    <select
                                        wire:model="lines.{{ $index }}.item_id"
                                        wire:change="selectItem({{ $index }}, $event.target.value)"
                                    >
                                        <option value="">اختر الصنف</option>
                                        @foreach($availableItems as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="erp-report-field">
                                    <label>الوحدة</label>
                                    <input type="text" value="{{ $line['unit_name'] ?? '' }}" disabled>
                                </div>

                                <div class="erp-report-field">
                                    <label>المتاح</label>
                                    <input type="text" value="{{ $number($line['available_quantity'] ?? 0) }}" disabled>
                                </div>

                                <div class="erp-report-field">
                                    <label>الكمية</label>
                                    <input type="number" step="0.001" min="0.001" wire:model.live="lines.{{ $index }}.quantity">
                                </div>

                                <div class="erp-report-field">
                                    <label>السعر</label>
                                    <input type="number" step="0.01" min="0.01" wire:model.live="lines.{{ $index }}.unit_price">
                                </div>

                                <button type="button" class="sales-desk-remove-btn" wire:click="removeLine({{ $index }})">
                                    حذف
                                </button>

                                <div class="erp-report-field">
                                    <label>خصم %</label>
                                    <input type="number" step="0.01" min="0" wire:model.live="lines.{{ $index }}.discount_percent">
                                </div>

                                <div class="erp-report-field" style="grid-column: span 2;">
                                    <label>ملاحظات</label>
                                    <input type="text" wire:model.live="lines.{{ $index }}.notes">
                                </div>

                                <div class="erp-report-field">
                                    <label>إجمالي السطر</label>
                                    <input type="text" value="{{ $money($this->lineTotal($line)) }}" disabled>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" class="erp-report-btn erp-report-btn-secondary" wire:click="addLine">
                            إضافة صنف
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <div class="sales-desk-total-box">
                    <div class="sales-desk-total-label">إجمالي الفاتورة</div>
                    <div class="sales-desk-total-value">{{ $money($this->grandTotal()) }}</div>
                </div>

                <div class="erp-report-card">
                    <div class="erp-report-card-body">
                        <div class="erp-report-field">
                            <label>طريقة الدفع</label>
                            <select wire:model.live="paymentMode">
                                <option value="cash">دفع كامل</option>
                                <option value="partial">دفع جزئي</option>
                                <option value="credit">آجل</option>
                            </select>
                        </div>

                        @if($paymentMode !== 'credit')
                            <div class="erp-report-field" style="margin-top: 12px;">
                                <label>الخزينة</label>
                                <select wire:model.live="cashboxId">
                                    @foreach($cashboxes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if($paymentMode === 'partial')
                            <div class="erp-report-field" style="margin-top: 12px;">
                                <label>المبلغ المدفوع</label>
                                <input type="number" step="0.01" min="0" wire:model.live="paidAmount">
                            </div>
                        @endif

                        <div style="margin-top: 18px;">
                            <div class="sales-desk-side-line">
                                <span>الإجمالي</span>
                                <span>{{ $money($this->grandTotal()) }}</span>
                            </div>

                            <div class="sales-desk-side-line">
                                <span>المتبقي على العميل</span>
                                <span>{{ $money($this->remainingAmount()) }}</span>
                            </div>
                        </div>

                        <div class="erp-report-field" style="margin-top: 14px;">
                            <label>ملاحظات</label>
                            <textarea rows="3" wire:model.live="notes"></textarea>
                        </div>

                        <button type="button" class="erp-report-btn erp-report-btn-primary" style="width: 100%; margin-top: 16px;" wire:click="submitSale">
                            إنهاء البيع
                        </button>
                        
                        @if($lastInvoiceId)
                            <a
                                href="{{ route('admin.prints.sales-invoices.receipt', $lastInvoiceId) }}"
                                target="_blank"
                                class="erp-report-btn erp-report-btn-secondary"
                                style="width: 100%; margin-top: 10px; text-align: center;"
                            >
                                طباعة آخر فاتورة {{ $lastInvoiceNumber }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>