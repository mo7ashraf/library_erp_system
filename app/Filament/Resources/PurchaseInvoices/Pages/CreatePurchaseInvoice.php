<?php

namespace App\Filament\Resources\PurchaseInvoices\Pages;

use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\Inventory\StockService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePurchaseInvoice extends CreateRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $warehouse = Warehouse::find($data['warehouse_id']);

        $data['branch_id'] = $warehouse?->branch_id;
        $data['user_id'] = auth()->id();
        $data['status'] = PurchaseInvoice::STATUS_DRAFT;
        $data['subtotal'] = 0;
        $data['grand_total'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $this->record->load(['warehouse', 'items']);

            if ($this->record->status === PurchaseInvoice::STATUS_POSTED) {
                return;
            }

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
                    'movement_type' => StockMovement::TYPE_PURCHASE,
                    'reference_type' => PurchaseInvoice::class,
                    'reference_id' => $this->record->id,
                    'reference_number' => $this->record->invoice_number,
                    'movement_date' => $this->record->invoice_date,
                    'notes' => $this->record->notes,
                ]);
            }

            $this->record->update([
                'status' => PurchaseInvoice::STATUS_POSTED,
                'posted_at' => now(),
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}