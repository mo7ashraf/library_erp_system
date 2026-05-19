<?php

namespace App\Filament\Pages;

use App\Services\Reports\SalesReportService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class SalesSummaryReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 41;

    protected string $view = 'filament.pages.sales-summary-report';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $report = [];

    public static function getNavigationLabel(): string
    {
        return 'تقرير المبيعات';
    }

    public function getTitle(): string
    {
        return 'تقرير المبيعات';
    }

    public function mount(): void
    {
        $this->fromDate = request()->query('from_date') ?: now()->startOfMonth()->toDateString();
        $this->toDate = request()->query('to_date') ?: now()->toDateString();

        $this->report = app(SalesReportService::class)->summary(
            fromDate: $this->fromDate,
            toDate: $this->toDate
        );
    }
}