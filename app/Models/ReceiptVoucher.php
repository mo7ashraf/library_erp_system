<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptVoucher extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';

    public const PARTY_CUSTOMER = 'customer';
    public const PARTY_SUPPLIER = 'supplier';
    public const PARTY_OTHER = 'other';

    public const TYPE_CUSTOMER_COLLECTION = 'customer_collection';
    public const TYPE_SUPPLIER_REFUND = 'supplier_refund';
    public const TYPE_GENERAL_INCOME = 'general_income';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'branch_id',
        'user_id',
        'cashbox_id',
        'bank_account_id',
        'customer_id',
        'supplier_id',
        'finance_category_id',
        'treasury_transaction_id',
        'voucher_number',
        'voucher_date',
        'payment_channel',
        'voucher_type',
        'party_type',
        'party_name',
        'amount',
        'status',
        'posted_at',
        'description',
        'notes',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'amount' => 'decimal:2',
        'posted_at' => 'datetime',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'finance_category_id');
    }

    public function treasuryTransaction(): BelongsTo
    {
        return $this->belongsTo(TreasuryTransaction::class);
    }

    public function resolvedPartyName(): string
    {
        return match ($this->party_type) {
            self::PARTY_CUSTOMER => $this->customer?->name ?? $this->party_name ?? '-',
            self::PARTY_SUPPLIER => $this->supplier?->name ?? $this->party_name ?? '-',
            default => $this->party_name ?? $this->category?->name ?? '-',
        };
    }
    public function voucherTypeLabel(): string
    {
        return match ($this->voucher_type) {
            self::TYPE_CUSTOMER_COLLECTION => 'تحصيل من عميل',
            self::TYPE_SUPPLIER_REFUND => 'استرداد من مورد',
            self::TYPE_GENERAL_INCOME => 'إيراد عام',
            self::TYPE_OTHER => 'أخرى',
            default => '-',
        };
    }
}