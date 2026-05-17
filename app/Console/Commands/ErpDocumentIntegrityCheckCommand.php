<?php

namespace App\Console\Commands;

use App\Models\DamagedStockDocument;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\StockCountDocument;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\WarehouseItemBalance;
use Illuminate\Console\Command;

class ErpDocumentIntegrityCheckCommand extends Command
{
    protected $signature = 'erp:check-documents';

    protected $description = 'Checks saved ERP documents, stock movements, balances, totals, and return limits.';

    private int $errors = 0;

    public function handle(): int
    {
        $this->info('Starting ERP document integrity check...');
        $this->line('------------------------------------------');

        $this->checkWarehouseBalancesAgainstMovements();
        $this->checkPurchaseInvoices();
        $this->checkSalesInvoices();
        $this->checkSalesReturns();
        $this->checkPurchaseReturns();
        $this->checkStockTransfers();
        $this->checkStockCounts();
        $this->checkDamagedStockDocuments();
        $this->checkSalesReturnLimits();
        $this->checkPurchaseReturnLimits();

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("ERP document integrity check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ ERP document integrity check passed.');

        return self::SUCCESS;
    }

    private function checkWarehouseBalancesAgainstMovements(): void
    {
        $this->info('Checking warehouse balances against stock movements...');

        $balances = WarehouseItemBalance::query()->get();

        foreach ($balances as $balance) {
            $movements = StockMovement::query()
                ->where('warehouse_id', $balance->warehouse_id)
                ->where('item_id', $balance->item_id)
                ->orderBy('id')
                ->get();

            if ($movements->isEmpty()) {
                $this->line("✓ Warehouse balance WH#{$balance->warehouse_id} ITEM#{$balance->item_id} has no movements yet.");
                continue;
            }

            $actualBalance = (float) $balance->quantity;
            $latestMovementBalance = (float) $movements->last()->balance_after;

            /*
            * Main reliable check:
            * The current warehouse balance must match the latest movement balance_after.
            */
            $this->assertClose(
                $actualBalance,
                $latestMovementBalance,
                "Latest movement balance_after WH#{$balance->warehouse_id} ITEM#{$balance->item_id}",
                0.001
            );

            /*
            * Diagnostic check:
            * Calculate movement-only balance from zero.
            * If different from current balance, this means there is an implied opening balance
            * from old seed/dev data or direct balance entry.
            */
            $inQty = (float) $movements
                ->where('direction', StockMovement::DIRECTION_IN)
                ->sum('quantity');

            $outQty = (float) $movements
                ->where('direction', StockMovement::DIRECTION_OUT)
                ->sum('quantity');

            $movementOnlyBalance = $inQty - $outQty;
            $impliedOpeningBalance = $actualBalance - $movementOnlyBalance;

            if (abs($impliedOpeningBalance) > 0.001) {
                $this->line(
                    "  Note: WH#{$balance->warehouse_id} ITEM#{$balance->item_id} has implied opening balance "
                    . number_format($impliedOpeningBalance, 3)
                    . " from seed/dev data."
                );
            } else {
                $this->line("✓ Warehouse movement total WH#{$balance->warehouse_id} ITEM#{$balance->item_id}");
            }
        }
    }

    private function checkPurchaseInvoices(): void
    {
        $this->info('Checking purchase invoices...');

        PurchaseInvoice::query()
            ->with('items')
            ->get()
            ->each(function (PurchaseInvoice $invoice): void {
                $subtotal = (float) $invoice->items->sum('line_total');
                $grandTotal = max(0, $subtotal - (float) $invoice->discount_amount + (float) $invoice->additional_cost);

                $this->assertClose((float) $invoice->subtotal, $subtotal, "Purchase invoice {$invoice->invoice_number} subtotal");
                $this->assertClose((float) $invoice->grand_total, $grandTotal, "Purchase invoice {$invoice->invoice_number} grand total");

                if ($invoice->status === PurchaseInvoice::STATUS_POSTED) {
                    $documentQty = (float) $invoice->items->sum('quantity');

                    $movementQty = (float) StockMovement::query()
                        ->where('movement_type', StockMovement::TYPE_PURCHASE)
                        ->where('reference_type', PurchaseInvoice::class)
                        ->where('reference_id', $invoice->id)
                        ->sum('quantity');

                    $this->assertClose($movementQty, $documentQty, "Purchase invoice {$invoice->invoice_number} stock movement quantity", 0.001);
                }
            });
    }

    private function checkSalesInvoices(): void
    {
        $this->info('Checking sales invoices...');

        SalesInvoice::query()
            ->with('items')
            ->get()
            ->each(function (SalesInvoice $invoice): void {
                $subtotal = (float) $invoice->items->sum('line_total');
                $commissionAmount = $subtotal * ((float) $invoice->commission_percent / 100);
                $grandTotal = max(
                    0,
                    $subtotal
                    - (float) $invoice->discount_amount
                    + (float) $invoice->service_amount
                    + $commissionAmount
                );

                $this->assertClose((float) $invoice->subtotal, $subtotal, "Sales invoice {$invoice->invoice_number} subtotal");
                $this->assertClose((float) $invoice->commission_amount, $commissionAmount, "Sales invoice {$invoice->invoice_number} commission");
                $this->assertClose((float) $invoice->grand_total, $grandTotal, "Sales invoice {$invoice->invoice_number} grand total");

                if ($invoice->status === SalesInvoice::STATUS_POSTED) {
                    $documentQty = (float) $invoice->items->sum('quantity');

                    $movementQty = (float) StockMovement::query()
                        ->where('movement_type', StockMovement::TYPE_SALE)
                        ->where('reference_type', SalesInvoice::class)
                        ->where('reference_id', $invoice->id)
                        ->sum('quantity');

                    $this->assertClose($movementQty, $documentQty, "Sales invoice {$invoice->invoice_number} stock movement quantity", 0.001);
                }
            });
    }

    private function checkSalesReturns(): void
    {
        $this->info('Checking sales returns...');

        SalesReturn::query()
            ->with('items')
            ->get()
            ->each(function (SalesReturn $return): void {
                $subtotal = (float) $return->items->sum('line_total');
                $grandTotal = max(0, $subtotal - (float) $return->discount_amount);

                $this->assertClose((float) $return->subtotal, $subtotal, "Sales return {$return->return_number} subtotal");
                $this->assertClose((float) $return->grand_total, $grandTotal, "Sales return {$return->return_number} grand total");

                if ($return->status === SalesReturn::STATUS_POSTED) {
                    $documentQty = (float) $return->items->sum('quantity');

                    $movementQty = (float) StockMovement::query()
                        ->where('movement_type', StockMovement::TYPE_SALE_RETURN)
                        ->where('reference_type', SalesReturn::class)
                        ->where('reference_id', $return->id)
                        ->sum('quantity');

                    $this->assertClose($movementQty, $documentQty, "Sales return {$return->return_number} stock movement quantity", 0.001);
                }
            });
    }

    private function checkPurchaseReturns(): void
    {
        $this->info('Checking purchase returns...');

        PurchaseReturn::query()
            ->with('items')
            ->get()
            ->each(function (PurchaseReturn $return): void {
                $subtotal = (float) $return->items->sum('line_total');
                $grandTotal = max(0, $subtotal - (float) $return->discount_amount);

                $this->assertClose((float) $return->subtotal, $subtotal, "Purchase return {$return->return_number} subtotal");
                $this->assertClose((float) $return->grand_total, $grandTotal, "Purchase return {$return->return_number} grand total");

                if ($return->status === PurchaseReturn::STATUS_POSTED) {
                    $documentQty = (float) $return->items->sum('quantity');

                    $movementQty = (float) StockMovement::query()
                        ->where('movement_type', StockMovement::TYPE_PURCHASE_RETURN)
                        ->where('reference_type', PurchaseReturn::class)
                        ->where('reference_id', $return->id)
                        ->sum('quantity');

                    $this->assertClose($movementQty, $documentQty, "Purchase return {$return->return_number} stock movement quantity", 0.001);
                }
            });
    }

    private function checkStockTransfers(): void
    {
        $this->info('Checking stock transfers...');

        StockTransfer::query()
            ->with('items')
            ->get()
            ->each(function (StockTransfer $transfer): void {
                $totalQuantity = (float) $transfer->items->sum('quantity');
                $totalCost = (float) $transfer->items->sum('total_cost');

                $this->assertClose((float) $transfer->total_quantity, $totalQuantity, "Stock transfer {$transfer->transfer_number} total quantity", 0.001);
                $this->assertClose((float) $transfer->total_cost, $totalCost, "Stock transfer {$transfer->transfer_number} total cost");

                if ($transfer->status === StockTransfer::STATUS_POSTED) {
                    $outQty = (float) StockMovement::query()
                        ->where('movement_type', StockMovement::TYPE_TRANSFER_OUT)
                        ->where('reference_type', StockTransfer::class)
                        ->where('reference_id', $transfer->id)
                        ->sum('quantity');

                    $inQty = (float) StockMovement::query()
                        ->where('movement_type', StockMovement::TYPE_TRANSFER_IN)
                        ->where('reference_type', StockTransfer::class)
                        ->where('reference_id', $transfer->id)
                        ->sum('quantity');

                    $this->assertClose($outQty, $totalQuantity, "Stock transfer {$transfer->transfer_number} transfer out quantity", 0.001);
                    $this->assertClose($inQty, $totalQuantity, "Stock transfer {$transfer->transfer_number} transfer in quantity", 0.001);
                }
            });
    }

    private function checkStockCounts(): void
    {
        $this->info('Checking stock count documents...');

        StockCountDocument::query()
            ->with('items')
            ->get()
            ->each(function (StockCountDocument $document): void {
                $increase = 0;
                $decrease = 0;
                $cost = 0;

                foreach ($document->items as $line) {
                    $difference = (float) $line->difference_quantity;

                    if ($difference > 0) {
                        $increase += $difference;
                    }

                    if ($difference < 0) {
                        $decrease += abs($difference);
                    }

                    $cost += abs((float) $line->difference_cost);
                }

                $this->assertClose((float) $document->total_increase_quantity, $increase, "Stock count {$document->count_number} increase quantity", 0.001);
                $this->assertClose((float) $document->total_decrease_quantity, $decrease, "Stock count {$document->count_number} decrease quantity", 0.001);
                $this->assertClose((float) $document->total_difference_cost, $cost, "Stock count {$document->count_number} difference cost");
            });
    }

    private function checkDamagedStockDocuments(): void
    {
        $this->info('Checking damaged stock documents...');

        DamagedStockDocument::query()
            ->with('items')
            ->get()
            ->each(function (DamagedStockDocument $document): void {
                $totalQuantity = (float) $document->items->sum('quantity');
                $totalCost = (float) $document->items->sum('total_cost');

                $this->assertClose((float) $document->total_quantity, $totalQuantity, "Damaged stock {$document->document_number} total quantity", 0.001);
                $this->assertClose((float) $document->total_cost, $totalCost, "Damaged stock {$document->document_number} total cost");

                if ($document->status === DamagedStockDocument::STATUS_POSTED) {
                    $movementQty = (float) StockMovement::query()
                        ->where('movement_type', StockMovement::TYPE_DAMAGED)
                        ->where('reference_type', DamagedStockDocument::class)
                        ->where('reference_id', $document->id)
                        ->sum('quantity');

                    $this->assertClose($movementQty, $totalQuantity, "Damaged stock {$document->document_number} movement quantity", 0.001);
                }
            });
    }

    private function checkSalesReturnLimits(): void
    {
        $this->info('Checking sales return limits...');

        SalesInvoiceItem::query()
            ->selectRaw('sales_invoice_id, item_id, SUM(quantity) as original_quantity')
            ->groupBy('sales_invoice_id', 'item_id')
            ->get()
            ->each(function ($row): void {
                $returnedQty = (float) SalesReturnItem::query()
                    ->where('item_id', $row->item_id)
                    ->whereHas('salesReturn', function ($query) use ($row) {
                        $query
                            ->where('sales_invoice_id', $row->sales_invoice_id)
                            ->where('status', SalesReturn::STATUS_POSTED);
                    })
                    ->sum('quantity');

                if ($returnedQty > (float) $row->original_quantity + 0.001) {
                    $this->addCheckError(
                        "Sales return exceeds original quantity. SalesInvoice#{$row->sales_invoice_id}, Item#{$row->item_id}, original {$row->original_quantity}, returned {$returnedQty}"
                    );
                }
            });
    }

    private function checkPurchaseReturnLimits(): void
    {
        $this->info('Checking purchase return limits...');

        PurchaseInvoiceItem::query()
            ->selectRaw('purchase_invoice_id, item_id, SUM(quantity) as original_quantity')
            ->groupBy('purchase_invoice_id', 'item_id')
            ->get()
            ->each(function ($row): void {
                $returnedQty = (float) PurchaseReturnItem::query()
                    ->where('item_id', $row->item_id)
                    ->whereHas('purchaseReturn', function ($query) use ($row) {
                        $query
                            ->where('purchase_invoice_id', $row->purchase_invoice_id)
                            ->where('status', PurchaseReturn::STATUS_POSTED);
                    })
                    ->sum('quantity');

                if ($returnedQty > (float) $row->original_quantity + 0.001) {
                    $this->addCheckError(
                        "Purchase return exceeds original quantity. PurchaseInvoice#{$row->purchase_invoice_id}, Item#{$row->item_id}, original {$row->original_quantity}, returned {$returnedQty}"
                    );
                }
            });
    }

    private function assertClose(float $actual, float $expected, string $label, float $tolerance = 0.01): void
    {
        if (abs($actual - $expected) > $tolerance) {
            $this->addCheckError("{$label}: expected {$expected}, actual {$actual}");

            return;
        }

        $this->line("✓ {$label}");
    }

    private function addCheckError(string $message): void
    {
        $this->errors++;

        $this->error("✗ {$message}");
    }

}