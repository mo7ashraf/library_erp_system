<?php

namespace App\Filament\Pages;

use App\Services\Reports\InventoryReportService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class InventorySummaryReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 43;

    protected string $view = 'filament.pages.inventory-summary-report';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public array $report = [];

    public static function getNavigationLabel(): string
    {
        return 'تقرير المخزون';
    }

    public function getTitle(): string
    {
        return 'تقرير المخزون';
    }

    public function mount(): void
    {
        $this->fromDate = request()->query('from_date') ?: now()->startOfMonth()->toDateString();
        $this->toDate = request()->query('to_date') ?: now()->toDateString();

        $this->report = app(InventoryReportService::class)->summary(
            fromDate: $this->fromDate,
            toDate: $this->toDate
        );
    }
}