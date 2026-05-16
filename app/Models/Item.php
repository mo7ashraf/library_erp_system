<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_group_id',
        'item_subgroup_id',
        'base_unit_id',
        'middle_unit_id',
        'large_unit_id',
        'code',
        'origin_code',
        'barcode',
        'name',
        'source',
        'publisher',
        'purchase_price',
        'first_discount_percent',
        'second_discount_percent',
        'net_purchase_price',
        'total_cost',
        'profit_margin_percent',
        'student_price',
        'teacher_price',
        'representative_price',
        'retail_price',
        'wholesale_price',
        'teacher_discount_percent',
        'representative_discount_percent',
        'return_percent',
        'max_stock',
        'min_stock',
        'reorder_level',
        'units_per_middle',
        'units_per_large',
        'image_path',
        'details',
        'notes',
        'continue_balance',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'first_discount_percent' => 'decimal:2',
        'second_discount_percent' => 'decimal:2',
        'net_purchase_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'profit_margin_percent' => 'decimal:2',
        'student_price' => 'decimal:2',
        'teacher_price' => 'decimal:2',
        'representative_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'teacher_discount_percent' => 'decimal:2',
        'representative_discount_percent' => 'decimal:2',
        'return_percent' => 'decimal:2',
        'max_stock' => 'decimal:3',
        'min_stock' => 'decimal:3',
        'reorder_level' => 'decimal:3',
        'continue_balance' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ItemGroup::class, 'item_group_id');
    }

    public function subgroup(): BelongsTo
    {
        return $this->belongsTo(ItemSubgroup::class, 'item_subgroup_id');
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function middleUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'middle_unit_id');
    }

    public function largeUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'large_unit_id');
    }
}