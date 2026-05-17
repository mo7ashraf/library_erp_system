<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamagedStockDocument extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';

    public const REASON_DAMAGED = 'damaged';
    public const REASON_LOST = 'lost';
    public const REASON_EXPIRED = 'expired';
    public const REASON_OTHER = 'other';

    protected $fillable = [
        'warehouse_id',
        'branch_id',
        'user_id',
        'document_number',
        'document_date',
        'reason_type',
        'status',
        'total_quantity',
        'total_cost',
        'posted_at',
        'notes',
    ];

    protected $casts = [
        'document_date' => 'date',
        'posted_at' => 'datetime',
        'total_quantity' => 'decimal:3',
        'total_cost' => 'decimal:2',
    ];

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
        return $this->hasMany(DamagedStockDocumentItem::class);
    }

    public function recalculateTotals(): void
    {
        $this->update([
            'total_quantity' => (float) $this->items()->sum('quantity'),
            'total_cost' => (float) $this->items()->sum('total_cost'),
        ]);
    }
}