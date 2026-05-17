<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreasuryTransaction extends Model
{
    use HasFactory;

    public const CHANNEL_CASH = 'cash';
    public const CHANNEL_BANK = 'bank';

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    public const TYPE_OPENING_BALANCE = 'opening_balance';
    public const TYPE_CUSTOMER_RECEIPT = 'customer_receipt';
    public const TYPE_SUPPLIER_PAYMENT = 'supplier_payment';
    public const TYPE_EXPENSE = 'expense';
    public const TYPE_INCOME = 'income';
    public const TYPE_SALES_INVOICE = 'sales_invoice';
    public const TYPE_PURCHASE_INVOICE = 'purchase_invoice';
    public const TYPE_SALES_RETURN = 'sales_return';
    public const TYPE_PURCHASE_RETURN = 'purchase_return';
    public const TYPE_MANUAL_ADJUSTMENT = 'manual_adjustment';

    protected $fillable = [
        'branch_id',
        'user_id',
        'cashbox_id',
        'bank_account_id',
        'transaction_number',
        'transaction_date',
        'payment_channel',
        'direction',
        'transaction_type',
        'party_type',
        'party_id',
        'party_name',
        'reference_type',
        'reference_id',
        'reference_number',
        'amount',
        'balance_after',
        'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}