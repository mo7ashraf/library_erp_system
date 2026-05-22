<?php

namespace App\Filament\Resources\SalesInvoices\Pages;

use App\Filament\Resources\SalesInvoices\SalesInvoiceResource;
use App\Models\SalesInvoice;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseItemBalance;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSalesInvoice extends CreateRecord
{
    protected static string $resource = SalesInvoiceResource::class;

    protected function beforeCreate(): void
    {
        $warehouseId = (int) ($this->data['warehouse_id'] ?? 0);
        $lines = $this->data['items'] ?? [];

        foreach ($lines as $line) {
            $itemId = (int) ($line['item_id'] ?? 0);
            $quantity = (float) ($line['quantity'] ?? 0);
            $unitPrice = (float) ($line['unit_price'] ?? 0);

            if ($itemId <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'يجب اختيار الصنف.',
                ]);
            }

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'كمية البيع يجب أن تكون أكبر من صفر.',
                ]);
            }

            if ($unitPrice <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'لا يمكن إنشاء فاتورة بيع بسعر صفر. راجع سعر الصنف.',
                ]);
            }

            $availableQuantity = (float) WarehouseItemBalance::query()
                ->where('warehouse_id', $warehouseId)
                ->where('item_id', $itemId)
                ->value('quantity');

            if ($availableQuantity <= 0) {
                throw ValidationException::withMessages([
                    'data.items' => 'هذا الصنف غير متاح في المخزن المحدد.',
                ]);
            }

            if ($quantity > $availableQuantity) {
                throw ValidationException::withMessages([
                    'data.items' => 'كمية البيع لا يمكن أن تتجاوز المتاح في المخزن. المتاح حاليًا: '
                        . number_format($availableQuantity, 3),
                ]);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $warehouse = Warehouse::find($data['warehouse_id']);

        $data['branch_id'] = $warehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = SalesInvoice::STATUS_DRAFT;
        $data['subtotal'] = 0;
        $data['grand_total'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['warehouse', 'items']);

            if ($this->record->status === SalesInvoice::STATUS_POSTED) {
                return;
            }

            $this->record->recalculateTotals();

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $averageCost = (float) WarehouseItemBalance::query()
                    ->where('warehouse_id', $this->record->warehouse_id)
                    ->where('item_id', $line->item_id)
                    ->value('average_cost');

                $stockService->decrease([
                    'warehouse_id' => $this->record->warehouse_id,
                    'item_id' => $line->item_id,
                    'branch_id' => $this->record->branch_id,
                    'user_id' => auth()->id(),
                    'quantity' => $line->quantity,
                    'unit_cost' => $averageCost,
                    'movement_type' => StockMovement::TYPE_SALE,
                    'reference_type' => SalesInvoice::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->invoice_number,
                    'movement_date' => $this->record->invoice_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->update([
                'status' => SalesInvoice::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}