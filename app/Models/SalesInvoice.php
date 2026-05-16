<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoice extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';

    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_CREDIT = 'credit';
    public const PAYMENT_PARTIAL = 'partial';

    public const PRICE_STUDENT = 'student';
    public const PRICE_TEACHER = 'teacher';
    public const PRICE_REPRESENTATIVE = 'representative';
    public const PRICE_RETAIL = 'retail';
    public const PRICE_WHOLESALE = 'wholesale';

    protected $fillable = [
        'customer_id',
        'warehouse_id',
        'branch_id',
        'user_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'payment_type',
        'price_type',
        'status',
        'subtotal',
        'discount_amount',
        'service_amount',
        'commission_percent',
        'commission_amount',
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
        'service_amount' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

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
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('line_total');
        $commissionAmount = $subtotal * ((float) $this->commission_percent / 100);

        $grandTotal = $subtotal
            - (float) $this->discount_amount
            + (float) $this->service_amount
            + $commissionAmount;

        $this->update([
            'subtotal' => $subtotal,
            'commission_amount' => $commissionAmount,
            'grand_total' => max(0, $grandTotal),
        ]);
    }
}