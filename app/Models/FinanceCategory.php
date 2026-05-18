<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceCategory extends Model
{
    use HasFactory;

    public const TYPE_EXPENSE = 'expense';
    public const TYPE_INCOME = 'income';

    protected $fillable = [
        'code',
        'name',
        'type',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function receiptVouchers(): HasMany
    {
        return $this->hasMany(ReceiptVoucher::class);
    }

    public function paymentVouchers(): HasMany
    {
        return $this->hasMany(PaymentVoucher::class);
    }
}