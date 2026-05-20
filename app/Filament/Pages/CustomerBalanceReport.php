<?php

namespace App\Filament\Pages;

use App\Services\Reports\PartyBalanceReportService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class CustomerBalanceReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 44;

    protected string $view = 'filament.pages.customer-balance-report';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $report = [];

    public static function getNavigationLabel(): string
    {
        return 'أرصدة العملاء';
    }

    public function getTitle(): string
    {
        return 'أرصدة العملاء';
    }

    public function mount(): void
    {
        $this->fromDate = request()->query('from_date') ?: null;
        $this->toDate = request()->query('to_date') ?: now()->toDateString();

        $this->report = app(PartyBalanceReportService::class)->customerBalances(
            fromDate: $this->fromDate,
            toDate: $this->toDate
        );
    }
}