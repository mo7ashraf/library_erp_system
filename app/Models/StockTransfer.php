<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';

    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'from_branch_id',
        'to_branch_id',
        'user_id',
        'transfer_number',
        'transfer_date',
        'status',
        'total_quantity',
        'total_cost',
        'posted_at',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'posted_at' => 'datetime',
        'total_quantity' => 'decimal:3',
        'total_cost' => 'decimal:2',
    ];

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function recalculateTotals(): void
    {
        $this->update([
            'total_quantity' => (float) $this->items()->sum('quantity'),
            'total_cost' => (float) $this->items()->sum('total_cost'),
        ]);
    }
}