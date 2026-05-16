<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_OPENING_BALANCE = 'opening_balance';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_PURCHASE_RETURN = 'purchase_return';
    public const TYPE_SALE = 'sale';
    public const TYPE_SALE_RETURN = 'sale_return';
    public const TYPE_TRANSFER_IN = 'transfer_in';
    public const TYPE_TRANSFER_OUT = 'transfer_out';
    public const TYPE_STOCK_COUNT_INCREASE = 'stock_count_increase';
    public const TYPE_STOCK_COUNT_DECREASE = 'stock_count_decrease';
    public const TYPE_DAMAGED = 'damaged';
    public const TYPE_MANUAL_ADJUSTMENT = 'manual_adjustment';

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    protected $fillable = [
        'warehouse_id',
        'item_id',
        'branch_id',
        'user_id',
        'movement_type',
        'direction',
        'reference_type',
        'reference_id',
        'reference_number',
        'movement_date',
        'quantity',
        'unit_cost',
        'total_cost',
        'balance_after',
        'notes',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'balance_after' => 'decimal:3',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}