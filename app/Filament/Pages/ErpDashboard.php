<?php

namespace App\Filament\Pages;

use App\Services\Dashboard\ErpDashboardService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class ErpDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|UnitEnum|null $navigationGroup = 'لوحة التحكم';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.erp-dashboard';

    public array $dashboard = [];

    public static function getNavigationLabel(): string
    {
        return 'لوحة التحكم التنفيذية';
    }

    public function getTitle(): string
    {
        return 'لوحة التحكم التنفيذية';
    }

    public function mount(): void
    {
        $this->dashboard = app(ErpDashboardService::class)->summary();
    }
}