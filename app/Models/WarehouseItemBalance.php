<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseItemBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'item_id',
        'quantity',
        'average_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'average_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}