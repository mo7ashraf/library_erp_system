<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamagedStockDocumentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'damaged_stock_document_id',
        'item_id',
        'unit_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (DamagedStockDocumentItem $line): void {
            $line->total_cost = (float) $line->quantity * (float) $line->unit_cost;
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DamagedStockDocument::class, 'damaged_stock_document_id');
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