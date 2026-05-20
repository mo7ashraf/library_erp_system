<x-filament-panels::page>
    @include('filament.pages.partials.party-balance-report', [
        'report' => $report,
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        'printRoute' => route('admin.prints.customer-balance-report', [
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]),
        'partyLabel' => 'العميل',
        'partyPluralLabel' => 'العملاء',
    ])
</x-filament-panels::page>