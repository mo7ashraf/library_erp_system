<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockCountDocument extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';

    protected $fillable = [
        'warehouse_id',
        'branch_id',
        'user_id',
        'count_number',
        'count_date',
        'status',
        'total_increase_quantity',
        'total_decrease_quantity',
        'total_difference_cost',
        'posted_at',
        'notes',
    ];

    protected $casts = [
        'count_date' => 'date',
        'posted_at' => 'datetime',
        'total_increase_quantity' => 'decimal:3',
        'total_decrease_quantity' => 'decimal:3',
        'total_difference_cost' => 'decimal:2',
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
        return $this->hasMany(StockCountDocumentItem::class);
    }

    public function recalculateTotals(): void
    {
        $increase = 0;
        $decrease = 0;
        $cost = 0;

        foreach ($this->items as $line) {
            $difference = (float) $line->difference_quantity;

            if ($difference > 0) {
                $increase += $difference;
            }

            if ($difference < 0) {
                $decrease += abs($difference);
            }

            $cost += abs((float) $line->difference_cost);
        }

        $this->update([
            'total_increase_quantity' => $increase,
            'total_decrease_quantity' => $decrease,
            'total_difference_cost' => $cost,
        ]);
    }
}