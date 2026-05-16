<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'code',
        'name',
        'type',
        'phone',
        'mobile',
        'email',
        'governorate',
        'city',
        'address',
        'opening_balance',
        'balance_type',
        'discount_percent',
        'sales_at_purchase_price',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'sales_at_purchase_price' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}