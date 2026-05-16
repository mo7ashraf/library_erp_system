<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoice extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';

    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_CREDIT = 'credit';
    public const PAYMENT_PARTIAL = 'partial';

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'branch_id',
        'user_id',
        'invoice_number',
        'supplier_invoice_number',
        'invoice_date',
        'due_date',
        'payment_type',
        'status',
        'subtotal',
        'discount_amount',
        'additional_cost',
        'grand_total',
        'posted_at',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'posted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('line_total');

        $grandTotal = $subtotal
            - (float) $this->discount_amount
            + (float) $this->additional_cost;

        $this->update([
            'subtotal' => $subtotal,
            'grand_total' => max(0, $grandTotal),
        ]);
    }
}