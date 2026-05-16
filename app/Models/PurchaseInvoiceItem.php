<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id',
        'item_id',
        'unit_id',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'net_unit_price',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseInvoiceItem $line): void {
            $quantity = (float) $line->quantity;
            $unitPrice = (float) $line->unit_price;
            $discountPercent = (float) $line->discount_percent;

            $grossTotal = $quantity * $unitPrice;
            $discountAmount = $grossTotal * ($discountPercent / 100);
            $lineTotal = max(0, $grossTotal - $discountAmount);

            $line->discount_amount = $discountAmount;
            $line->line_total = $lineTotal;
            $line->net_unit_price = $quantity > 0 ? $lineTotal / $quantity : 0;
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}