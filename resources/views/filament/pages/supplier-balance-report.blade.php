<x-filament-panels::page>
    @include('filament.pages.partials.party-balance-report', [
        'report' => $report,
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        'printRoute' => route('admin.prints.supplier-balance-report', [
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]),
        'partyLabel' => 'المورد',
        'partyPluralLabel' => 'الموردين',
    ])
</x-filament-panels::page>