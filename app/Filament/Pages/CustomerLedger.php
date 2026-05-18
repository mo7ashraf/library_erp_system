<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Services\Finance\PartyLedgerService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class CustomerLedger extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'الحسابات والمالية';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.customer-ledger';

    public ?int $customerId = null;

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $customers = [];

    public array $ledger = [
        'party_name' => null,
        'party_code' => null,
        'from_date' => null,
        'to_date' => null,
        'opening_balance' => 0,
        'opening_balance_label' => '0.00',
        'total_debit' => 0,
        'total_credit' => 0,
        'closing_balance' => 0,
        'closing_balance_label' => '0.00',
        'rows' => [],
    ];

    public static function getNavigationLabel(): string
    {
        return 'كشف حساب عميل';
    }

    public function getTitle(): string
    {
        return 'كشف حساب عميل';
    }

    public function mount(): void
    {
        $this->customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $requestedCustomerId = request()->integer('customer_id');

        $this->customerId = $requestedCustomerId ?: (array_key_first($this->customers) ?: null);
        $this->fromDate = request()->query('from_date') ?: null;
        $this->toDate = request()->query('to_date') ?: null;

        $this->loadLedger();
    }

    private function loadLedger(): void
    {
        if (! $this->customerId) {
            return;
        }

        $this->ledger = app(PartyLedgerService::class)->customerLedger(
            customerId: $this->customerId,
            fromDate: $this->fromDate,
            toDate: $this->toDate
        );
    }
}