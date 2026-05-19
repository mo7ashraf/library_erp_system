<?php

namespace App\Filament\Pages;

use App\Services\Reports\PurchaseReportService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class PurchaseSummaryReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 42;

    protected string $view = 'filament.pages.purchase-summary-report';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $report = [];

    public static function getNavigationLabel(): string
    {
        return 'تقرير المشتريات';
    }

    public function getTitle(): string
    {
        return 'تقرير المشتريات';
    }

    public function mount(): void
    {
        $this->fromDate = request()->query('from_date') ?: now()->startOfMonth()->toDateString();
        $this->toDate = request()->query('to_date') ?: now()->toDateString();

        $this->report = app(PurchaseReportService::class)->summary(
            fromDate: $this->fromDate,
            toDate: $this->toDate
        );
    }
}