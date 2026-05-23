<?php

namespace App\Filament\Pages;

use App\Models\Cashbox;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ReceiptVoucher;
use App\Models\SalesInvoice;
use App\Models\StockMovement;
use App\Models\TreasuryTransaction;
use App\Models\Warehouse;
use App\Models\WarehouseItemBalance;
use App\Services\Finance\TreasuryService;
use App\Services\Inventory\StockService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use UnitEnum;
use Illuminate\Validation\ValidationException;

class SalesDesk extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|UnitEnum|null $navigationGroup = 'المبيعات';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.sales-desk';

    public ?int $customerId = null;

    public ?int $warehouseId = null;

    public ?int $cashboxId = null;

    public string $paymentMode = SalesInvoice::PAYMENT_CASH;

    public string $priceType = SalesInvoice::PRICE_STUDENT;

    public float $paidAmount = 0;

    public ?string $notes = null;

    public array $customers = [];

    public array $warehouses = [];

    public array $cashboxes = [];

    public array $availableItems = [];

    public array $lines = [];

    public ?int $lastInvoiceId = null;

    public ?string $lastInvoiceNumber = null;

    public static function getNavigationLabel(): string
    {
        return 'نقطة البيع';
    }

    public function getTitle(): string
    {
        return 'نقطة البيع';
    }

    public function mount(): void
    {
        $this->customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->warehouses = Warehouse::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->cashboxes = Cashbox::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->customerId = array_key_first($this->customers) ?: null;
        $this->warehouseId = array_key_first($this->warehouses) ?: null;
        $this->cashboxId = array_key_first($this->cashboxes) ?: null;

        $this->priceType = $this->priceTypeForCustomer($this->customerId);
        $this->reloadAvailableItems();
        $this->addLine();
    }

    public function updatedCustomerId(): void
    {
        $this->priceType = $this->priceTypeForCustomer($this->customerId);

        foreach (array_keys($this->lines) as $index) {
            $itemId = (int) ($this->lines[$index]['item_id'] ?? 0);

            if ($itemId > 0) {
                $this->lines[$index]['unit_price'] = $this->itemPrice($itemId);
            }
        }
    }

    public function updatedWarehouseId(): void
    {
        $this->reloadAvailableItems();
        $this->lines = [];
        $this->addLine();
    }

    public function updatedPriceType(): void
    {
        foreach (array_keys($this->lines) as $index) {
            $itemId = (int) ($this->lines[$index]['item_id'] ?? 0);

            if ($itemId > 0) {
                $this->lines[$index]['unit_price'] = $this->itemPrice($itemId);
            }
        }
    }

    public function addLine(): void
    {
        $this->lines[] = [
            'item_id' => null,
            'item_name' => '',
            'unit_id' => null,
            'unit_name' => '',
            'available_quantity' => 0,
            'quantity' => 1,
            'unit_price' => 0,
            'discount_percent' => 0,
            'notes' => null,
        ];
    }

    public function removeLine(int $index): void
    {
        unset($this->lines[$index]);

        $this->lines = array_values($this->lines);

        if (count($this->lines) === 0) {
            $this->addLine();
        }
    }

    public function selectItem(int $index, $itemId): void
    {
        $itemId = $itemId ? (int) $itemId : null;

        if (! isset($this->lines[$index])) {
            return;
        }

        if (! $itemId) {
            $this->lines[$index]['item_id'] = null;
            $this->lines[$index]['item_name'] = '';
            $this->lines[$index]['unit_id'] = null;
            $this->lines[$index]['unit_name'] = '';
            $this->lines[$index]['available_quantity'] = 0;
            $this->lines[$index]['quantity'] = 1;
            $this->lines[$index]['unit_price'] = 0;

            return;
        }

        $item = Item::with('baseUnit')->find($itemId);
        $balance = $this->getBalance($itemId);

        $this->lines[$index]['item_id'] = $itemId;
        $this->lines[$index]['item_name'] = $item?->name ?? '';
        $this->lines[$index]['unit_id'] = $item?->base_unit_id;
        $this->lines[$index]['unit_name'] = $item?->baseUnit?->name ?? '';
        $this->lines[$index]['available_quantity'] = $balance['quantity'];
        $this->lines[$index]['quantity'] = min(1, max(0, $balance['quantity']));
        $this->lines[$index]['unit_price'] = $this->itemPrice($itemId);
        $this->lines[$index]['discount_percent'] = 0;
    }

    public function submitSale(): void
    {
        $this->validateBeforeSubmit();

        DB::transaction(function (): void {
            $warehouse = Warehouse::findOrFail($this->warehouseId);

            $invoice = SalesInvoice::create([
                'customer_id' => $this->customerId,
                'warehouse_id' => $this->warehouseId,
                'branch_id' => $warehouse->branch_id,
                'user_id' => auth()->id(),
                'invoice_number' => 'SAL-' . now()->format('Ymd-His'),
                'invoice_date' => now()->toDateString(),
                'due_date' => null,
                'payment_type' => $this->resolvedPaymentType(),
                'price_type' => $this->priceType,
                'status' => SalesInvoice::STATUS_DRAFT,
                'subtotal' => 0,
                'discount_amount' => 0,
                'service_amount' => 0,
                'commission_percent' => 0,
                'commission_amount' => 0,
                'grand_total' => 0,
                'notes' => $this->notes,
            ]);

            foreach ($this->validLines() as $line) {
                $invoice->items()->create([
                    'item_id' => (int) $line['item_id'],
                    'unit_id' => $line['unit_id'],
                    'quantity' => (float) $line['quantity'],
                    'unit_price' => (float) $line['unit_price'],
                    'discount_percent' => (float) ($line['discount_percent'] ?? 0),
                    'notes' => $line['notes'] ?? null,
                ]);
            }

            $invoice->recalculateTotals();
            $invoice->refresh();

            $stockService = app(StockService::class);

            foreach ($invoice->items as $line) {
                $averageCost = (float) WarehouseItemBalance::query()
                    ->where('warehouse_id', $invoice->warehouse_id)
                    ->where('item_id', $line->item_id)
                    ->value('average_cost');

                $stockService->decrease([
                    'warehouse_id' => $invoice->warehouse_id,
                    'item_id' => $line->item_id,
                    'branch_id' => $invoice->branch_id,
                    'user_id' => auth()->id(),
                    'quantity' => $line->quantity,
                    'unit_cost' => $averageCost,
                    'movement_type' => StockMovement::TYPE_SALE,
                    'reference_type' => SalesInvoice::class,
                    'reference_id' => $invoice->id,
                    'reference_number' => $invoice->invoice_number,
                    'movement_date' => $invoice->invoice_date,
                    'notes' => $invoice->notes,
                ]);
            }

            $invoice->update([
                'status' => SalesInvoice::STATUS_POSTED,
                'posted_at' => now(),
            ]);

            $paidAmount = $this->resolvedPaidAmount((float) $invoice->grand_total);

            if ($paidAmount > 0) {
                $this->createReceiptVoucherForSale($invoice, $paidAmount);
            }

            $this->lastInvoiceId = $invoice->id;
            $this->lastInvoiceNumber = $invoice->invoice_number;

            Notification::make()
                ->title('تم إنشاء فاتورة البيع بنجاح')
                ->body('رقم الفاتورة: ' . $invoice->invoice_number)
                ->success()
                ->send();

            $this->resetDesk();
        });
    }

    private function createReceiptVoucherForSale(SalesInvoice $invoice, float $paidAmount): void
    {
        $customer = Customer::find($invoice->customer_id);
        $cashbox = Cashbox::findOrFail($this->cashboxId);

        $voucher = ReceiptVoucher::create([
            'voucher_number' => 'RCV-SALE-' . $invoice->id . '-' . now()->format('His'),
            'voucher_date' => now()->toDateString(),
            'voucher_type' => ReceiptVoucher::TYPE_CUSTOMER_COLLECTION,
            'party_type' => ReceiptVoucher::PARTY_CUSTOMER,
            'customer_id' => $invoice->customer_id,
            'supplier_id' => null,
            'finance_category_id' => null,
            'party_name' => $customer?->name ?? '-',
            'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
            'cashbox_id' => $this->cashboxId,
            'bank_account_id' => null,
            'amount' => $paidAmount,
            'description' => 'تحصيل من فاتورة بيع رقم ' . $invoice->invoice_number,
            'notes' => null,
            'branch_id' => $cashbox->branch_id,
            'user_id' => auth()->id(),
            'status' => ReceiptVoucher::STATUS_DRAFT,
        ]);

        $transaction = app(TreasuryService::class)->receive([
            'branch_id' => $cashbox->branch_id,
            'user_id' => auth()->id(),
            'cashbox_id' => $this->cashboxId,
            'bank_account_id' => null,
            'payment_channel' => TreasuryTransaction::CHANNEL_CASH,
            'transaction_number' => $voucher->voucher_number,
            'transaction_date' => $voucher->voucher_date,
            'transaction_type' => TreasuryTransaction::TYPE_CUSTOMER_RECEIPT,
            'party_type' => ReceiptVoucher::PARTY_CUSTOMER,
            'party_id' => $invoice->customer_id,
            'party_name' => $customer?->name ?? '-',
            'reference_type' => ReceiptVoucher::class,
            'reference_id' => $voucher->id,
            'reference_number' => $voucher->voucher_number,
            'amount' => $paidAmount,
            'description' => $voucher->description,
        ]);

        $voucher->update([
            'treasury_transaction_id' => $transaction->id,
            'status' => ReceiptVoucher::STATUS_POSTED,
            'posted_at' => now(),
        ]);
    }

    private function validateBeforeSubmit(): void
    {
        if (! $this->customerId) {
            $this->failWith('اختر العميل أولًا.');
        }

        if (! $this->warehouseId) {
            $this->failWith('اختر المخزن أولًا.');
        }

        if ($this->paymentMode !== SalesInvoice::PAYMENT_CREDIT && ! $this->cashboxId) {
            $this->failWith('اختر الخزينة عند وجود مبلغ مدفوع.');
        }

        $validLines = $this->validLines();

        if (count($validLines) === 0) {
            $this->failWith('أضف صنفًا واحدًا على الأقل.');
        }

        foreach ($validLines as $line) {
            $itemId = (int) $line['item_id'];
            $quantity = (float) $line['quantity'];
            $unitPrice = (float) $line['unit_price'];
            $available = $this->getBalance($itemId)['quantity'];

            if ($quantity <= 0) {
                $this->failWith('كمية البيع يجب أن تكون أكبر من صفر.');
            }

            if ($unitPrice <= 0) {
                $this->failWith('لا يمكن البيع بسعر صفر.');
            }

            if ($quantity > $available) {
                $this->failWith(
                    'كمية الصنف "' . ($line['item_name'] ?? '-') . '" أكبر من المتاح. المتاح: '
                    . number_format($available, 3)
                );
            }
        }

        $total = $this->grandTotal();

        if ($this->paymentMode === SalesInvoice::PAYMENT_PARTIAL) {
            if ($this->paidAmount <= 0 || $this->paidAmount >= $total) {
                $this->failWith('في الدفع الجزئي يجب أن يكون المبلغ المدفوع أكبر من صفر وأقل من إجمالي الفاتورة.');
            }
        }
    }

    private function failWith(string $message): void
    {
        Notification::make()
            ->title('تعذر إنشاء الفاتورة')
            ->body($message)
            ->danger()
            ->send();

        throw ValidationException::withMessages([
            'sales_desk' => $message,
        ]);
    }

    private function resetDesk(): void
    {
        $this->paymentMode = SalesInvoice::PAYMENT_CASH;
        $this->paidAmount = 0;
        $this->notes = null;
        $this->reloadAvailableItems();
        $this->lines = [];
        $this->addLine();
    }

    private function reloadAvailableItems(): void
    {
        if (! $this->warehouseId) {
            $this->availableItems = [];

            return;
        }

        $this->availableItems = WarehouseItemBalance::query()
            ->with('item')
            ->where('warehouse_id', $this->warehouseId)
            ->where('quantity', '>', 0)
            ->get()
            ->filter(fn (WarehouseItemBalance $balance): bool => filled($balance->item))
            ->mapWithKeys(function (WarehouseItemBalance $balance): array {
                return [
                    $balance->item_id => ($balance->item?->name ?? 'صنف غير معروف')
                        . ' — المتاح: '
                        . number_format((float) $balance->quantity, 3),
                ];
            })
            ->toArray();
    }

    private function getBalance(int $itemId): array
    {
        $balance = WarehouseItemBalance::query()
            ->where('warehouse_id', $this->warehouseId)
            ->where('item_id', $itemId)
            ->first();

        return [
            'quantity' => (float) ($balance?->quantity ?? 0),
            'average_cost' => (float) ($balance?->average_cost ?? 0),
        ];
    }

    private function validLines(): array
    {
        return collect($this->lines)
            ->filter(fn (array $line): bool => filled($line['item_id'] ?? null))
            ->values()
            ->toArray();
    }

    private function priceTypeForCustomer(?int $customerId): string
    {
        if (! $customerId) {
            return SalesInvoice::PRICE_STUDENT;
        }

        $customer = Customer::find($customerId);

        return match ($customer?->type) {
            'teacher' => SalesInvoice::PRICE_TEACHER,
            'representative' => SalesInvoice::PRICE_REPRESENTATIVE,
            'wholesale' => SalesInvoice::PRICE_WHOLESALE,
            default => SalesInvoice::PRICE_STUDENT,
        };
    }

    private function itemPrice(int $itemId): float
    {
        $item = Item::find($itemId);

        if (! $item) {
            return 0;
        }

        $customer = $this->customerId ? Customer::find($this->customerId) : null;

        if ($customer?->sales_at_purchase_price) {
            return (float) ($item->net_purchase_price ?: $item->purchase_price ?: 0);
        }

        return match ($this->priceType) {
            SalesInvoice::PRICE_TEACHER => (float) $item->teacher_price,
            SalesInvoice::PRICE_REPRESENTATIVE => (float) $item->representative_price,
            SalesInvoice::PRICE_RETAIL => (float) $item->retail_price,
            SalesInvoice::PRICE_WHOLESALE => (float) $item->wholesale_price,
            default => (float) $item->student_price,
        };
    }

    private function resolvedPaymentType(): string
    {
        return match ($this->paymentMode) {
            SalesInvoice::PAYMENT_CREDIT => SalesInvoice::PAYMENT_CREDIT,
            SalesInvoice::PAYMENT_PARTIAL => SalesInvoice::PAYMENT_PARTIAL,
            default => SalesInvoice::PAYMENT_CASH,
        };
    }

    private function resolvedPaidAmount(float $grandTotal): float
    {
        return match ($this->paymentMode) {
            SalesInvoice::PAYMENT_CREDIT => 0,
            SalesInvoice::PAYMENT_PARTIAL => (float) $this->paidAmount,
            default => $grandTotal,
        };
    }

    public function lineTotal(array $line): float
    {
        $quantity = (float) ($line['quantity'] ?? 0);
        $unitPrice = (float) ($line['unit_price'] ?? 0);
        $discountPercent = (float) ($line['discount_percent'] ?? 0);

        $gross = $quantity * $unitPrice;
        $discount = $gross * ($discountPercent / 100);

        return max(0, $gross - $discount);
    }

    public function subtotal(): float
    {
        return collect($this->validLines())
            ->sum(fn (array $line): float => $this->lineTotal($line));
    }

    public function grandTotal(): float
    {
        return $this->subtotal();
    }

    public function remainingAmount(): float
    {
        $paid = match ($this->paymentMode) {
            SalesInvoice::PAYMENT_CASH => $this->grandTotal(),
            SalesInvoice::PAYMENT_CREDIT => 0,
            default => (float) $this->paidAmount,
        };

        return max(0, $this->grandTotal() - $paid);
    }
}