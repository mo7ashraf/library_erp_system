<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpeningStockDocumentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_stock_document_id',
        'item_id',
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
        static::saving(function (OpeningStockDocumentItem $item): void {
            $item->total_cost = (float) $item->quantity * (float) $item->unit_cost;
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(OpeningStockDocument::class, 'opening_stock_document_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}