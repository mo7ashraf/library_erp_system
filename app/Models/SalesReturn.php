<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturn extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';

    public const REFUND_CASH = 'cash';
    public const REFUND_CREDIT_BALANCE = 'credit_balance';

    protected $fillable = [
        'sales_invoice_id',
        'customer_id',
        'warehouse_id',
        'branch_id',
        'user_id',
        'return_number',
        'return_date',
        'refund_type',
        'status',
        'subtotal',
        'discount_amount',
        'grand_total',
        'posted_at',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'posted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
        return $this->hasMany(SalesReturnItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('line_total');

        $grandTotal = $subtotal - (float) $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'grand_total' => max(0, $grandTotal),
        ]);
    }
}