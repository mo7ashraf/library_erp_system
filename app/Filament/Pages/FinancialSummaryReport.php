<?php

namespace App\Filament\Pages;

use App\Services\Finance\FinancialReportService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class FinancialSummaryReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 40;

    protected string $view = 'filament.pages.financial-summary-report';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $report = [];

    public static function getNavigationLabel(): string
    {
        return 'الملخص المالي';
    }

    public function getTitle(): string
    {
        return 'الملخص المالي';
    }

    public function mount(): void
    {
        $this->fromDate = request()->query('from_date') ?: now()->startOfMonth()->toDateString();
        $this->toDate = request()->query('to_date') ?: now()->toDateString();

        $this->report = app(FinancialReportService::class)->summary(
            fromDate: $this->fromDate,
            toDate: $this->toDate
        );
    }
}