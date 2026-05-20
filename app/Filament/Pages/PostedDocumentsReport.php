<?php

namespace App\Filament\Pages;

use App\Services\Reports\PostedDocumentsReportService;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class PostedDocumentsReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 46;

    protected string $view = 'filament.pages.posted-documents-report';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public string $status = 'all';

    public array $report = [];

    public static function getNavigationLabel(): string
    {
        return 'مراجعة المستندات';
    }

    public function getTitle(): string
    {
        return 'مراجعة المستندات';
    }

    public function mount(): void
    {
        $this->fromDate = request()->query('from_date') ?: now()->startOfMonth()->toDateString();
        $this->toDate = request()->query('to_date') ?: now()->toDateString();
        $this->status = request()->query('status') ?: 'all';

        $this->report = app(PostedDocumentsReportService::class)->summary(
            fromDate: $this->fromDate,
            toDate: $this->toDate,
            status: $this->status
        );
    }
}