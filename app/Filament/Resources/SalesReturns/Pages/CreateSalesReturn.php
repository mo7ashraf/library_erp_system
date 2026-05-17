<?php

namespace App\Filament\Resources\SalesReturns\Pages;

use App\Filament\Resources\SalesReturns\SalesReturnResource;
use App\Models\SalesReturn;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\SalesInvoiceItem;
use App\Models\SalesReturnItem;
use Illuminate\Validation\ValidationException;

class CreateSalesReturn extends CreateRecord
{
    protected static string $resource = SalesReturnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $warehouse = Warehouse::find($data['warehouse_id']);

        $data['branch_id'] = $warehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = SalesReturn::STATUS_DRAFT;
        $data['subtotal'] = 0;
        $data['grand_total'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['warehouse', 'items']);
            if ($this->record->status === SalesReturn::STATUS_POSTED) {
                return;
            }
            $this->validateReturnQuantities();

            $this->record->recalculateTotals();

            $stockService = app(StockService::class);

            foreach ($this->record->items as $line) {
                $stockService->increase([
                    'warehouse_id' => $this->record->warehouse_id,
                    'item_id' => $line->item_id,
                    'branch_id' => $this->record->branch_id,
                    'user_id' => auth()->id(),
                    'quantity' => $line->quantity,
                    'unit_cost' => $line->net_unit_price,
                    'movement_type' => StockMovement::TYPE_SALE_RETURN,
                    'reference_type' => SalesReturn::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->return_number,
                    'movement_date' => $this->record->return_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->update([
                'status' => SalesReturn::STATUS_POSTED,
                'posted_at' => now(),
            ]);
            
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    // private function validateReturnQuantities(): void
    // {
    //     if (! $this->record->sales_invoice_id) {
    //         throw ValidationException::withMessages([
    //             'sales_invoice_id' => 'يجب اختيار فاتورة البيع الأصلية قبل تسجيل المرتجع.',
    //         ]);
    //     }

    //     $invoiceId = $this->record->sales_invoice_id;

    //     $returnItems = $this->record->items
    //         ->groupBy('item_id')
    //         ->map(fn ($lines) => (float) $lines->sum('quantity'));

    //     foreach ($returnItems as $itemId => $newReturnQty) {
    //         $originalQty = (float) SalesInvoiceItem::query()
    //             ->where('sales_invoice_id', $invoiceId)
    //             ->where('item_id', $itemId)
    //             ->sum('quantity');

    //         $alreadyReturnedQty = (float) SalesReturnItem::query()
    //             ->where('item_id', $itemId)
    //             ->whereHas('salesReturn', function ($query) use ($invoiceId) {
    //                 $query
    //                     ->where('sales_invoice_id', $invoiceId)
    //                     ->where('status', SalesReturn::STATUS_POSTED);
    //             })
    //             ->sum('quantity');

    //         $availableQty = max(0, $originalQty - $alreadyReturnedQty);

    //         if ($originalQty <= 0) {
    //             throw ValidationException::withMessages([
    //                 'items' => 'يوجد صنف في المرتجع غير موجود في فاتورة البيع الأصلية.',
    //             ]);
    //         }

    //         if ($newReturnQty > $availableQty) {
    //             throw ValidationException::withMessages([
    //                 'items' => "لا يمكن إرجاع كمية {$newReturnQty}. الكمية المتاحة للمرتجع من هذا الصنف هي {$availableQty} فقط.",
    //             ]);
    //         }
    //     }
    // }
    private function validateReturnQuantities(): void
    {
        $invoiceId = $this->record->sales_invoice_id;

        if (! $invoiceId) {
            throw ValidationException::withMessages([
                'sales_invoice_id' => 'يجب اختيار فاتورة البيع الأصلية.',
            ]);
        }

        $returnItems = $this->record->items
            ->groupBy('item_id')
            ->map(fn ($lines) => (float) $lines->sum('quantity'));

        foreach ($returnItems as $itemId => $newReturnQty) {
            $originalQty = (float) SalesInvoiceItem::query()
                ->where('sales_invoice_id', $invoiceId)
                ->where('item_id', $itemId)
                ->sum('quantity');

            $alreadyReturnedQty = (float) SalesReturnItem::query()
                ->where('item_id', $itemId)
                ->whereHas('salesReturn', function ($query) use ($invoiceId) {
                    $query
                        ->where('sales_invoice_id', $invoiceId)
                        ->where('status', \App\Models\SalesReturn::STATUS_POSTED);
                })
                ->sum('quantity');

            $availableQty = max(0, $originalQty - $alreadyReturnedQty);

            if ($newReturnQty > $availableQty) {
                throw ValidationException::withMessages([
                    'items' => "لا يمكن إرجاع كمية {$newReturnQty}. الحد الأقصى المتاح هو {$availableQty}.",
                ]);
            }
        }
    }
}