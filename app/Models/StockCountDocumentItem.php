<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockCountDocumentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_count_document_id',
        'item_id',
        'unit_id',
        'system_quantity',
        'actual_quantity',
        'difference_quantity',
        'unit_cost',
        'difference_cost',
        'notes',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:3',
        'actual_quantity' => 'decimal:3',
        'difference_quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'difference_cost' => 'decimal:2',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(StockCountDocument::class, 'stock_count_document_id');
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