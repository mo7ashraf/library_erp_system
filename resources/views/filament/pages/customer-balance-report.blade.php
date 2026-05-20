<x-filament-panels::page>
    @include('filament.pages.partials.party-balance-report', [
        'report' => $report,
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        'printRoute' => null,
        'partyLabel' => 'العميل',
        'partyPluralLabel' => 'العملاء',
    ])
</x-filament-panels::page>