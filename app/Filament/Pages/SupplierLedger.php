<?php

namespace App\Filament\Pages;

use App\Models\Supplier;
use App\Services\Finance\PartyLedgerService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class SupplierLedger extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'الحسابات والمالية';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.supplier-ledger';

    public ?int $supplierId = null;

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $suppliers = [];

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
        return 'كشف حساب مورد';
    }

    public function getTitle(): string
    {
        return 'كشف حساب مورد';
    }

    public function mount(): void
    {
        $this->suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $requestedSupplierId = request()->integer('supplier_id');

        $this->supplierId = $requestedSupplierId ?: (array_key_first($this->suppliers) ?: null);
        $this->fromDate = request()->query('from_date') ?: null;
        $this->toDate = request()->query('to_date') ?: null;

        $this->loadLedger();
    }

    private function loadLedger(): void
    {
        if (! $this->supplierId) {
            return;
        }

        $this->ledger = app(PartyLedgerService::class)->supplierLedger(
            supplierId: $this->supplierId,
            fromDate: $this->fromDate,
            toDate: $this->toDate
        );
    }
}