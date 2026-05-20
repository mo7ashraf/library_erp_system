@props([
    'orientation' => 'portrait',
])

<style>
    @page {
        size: A4 {{ $orientation }};
        margin: {{ $orientation === 'landscape' ? '10mm' : '12mm' }};
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        direction: rtl;
        font-family: "Cairo", Tahoma, Arial, sans-serif;
        color: #111827;
        background: #e5e7eb;
        font-size: 12px;
    }

    body {
        padding: 20px;
    }

    .erp-print-actions {
        width: {{ $orientation === 'landscape' ? '297mm' : '210mm' }};
        max-width: 100%;
        margin: 0 auto 12px;
        display: flex;
        gap: 8px;
    }

    .erp-print-btn {
        border: none;
        background: #111827;
        color: white;
        padding: 9px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-family: "Cairo", sans-serif;
        font-weight: 800;
    }

    .erp-print-btn-secondary {
        background: #6b7280;
    }

    .erp-print-page {
        width: {{ $orientation === 'landscape' ? '297mm' : '210mm' }};
        min-height: {{ $orientation === 'landscape' ? '210mm' : '297mm' }};
        max-width: 100%;
        margin: 0 auto;
        background: white;
        padding: {{ $orientation === 'landscape' ? '12mm' : '14mm' }};
        border: 1px solid #d1d5db;
    }

    .erp-print-header {
        text-align: center;
        border-bottom: 3px solid #111827;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }

    .erp-print-header h1 {
        margin: 0;
        font-size: 25px;
        font-weight: 900;
    }

    .erp-print-header .subtitle {
        margin-top: 4px;
        color: #6b7280;
        font-weight: 700;
    }

    .erp-print-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    .erp-print-box {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 10px;
    }

    .erp-print-box-title {
        margin: 0 0 8px;
        font-size: 14px;
        font-weight: 900;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 6px;
    }

    .erp-print-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin: 5px 0;
    }

    .erp-print-label {
        color: #6b7280;
        font-weight: 700;
    }

    .erp-print-value {
        font-weight: 900;
        text-align: left;
    }

    .erp-print-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 14px;
    }

    .erp-print-summary-card {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 10px;
        background: #f9fafb;
    }

    .erp-print-summary-label {
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
    }

    .erp-print-summary-value {
        margin-top: 5px;
        font-size: 15px;
        font-weight: 900;
    }

    .erp-print-section-title {
        margin: 18px 0 8px;
        font-size: 16px;
        font-weight: 900;
        border-right: 5px solid #111827;
        padding-right: 8px;
    }

    .erp-print-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        table-layout: fixed;
    }

    .erp-print-table th,
    .erp-print-table td {
        border: 1px solid #cfd6df;
        padding: 7px;
        vertical-align: top;
        line-height: 1.7;
    }

    .erp-print-table th {
        background: #f3f4f6;
        color: #111827;
        font-weight: 900;
        text-align: right;
        white-space: nowrap;
    }

    .erp-print-table td {
        color: #111827;
        font-weight: 600;
    }

    .erp-print-compact-table {
        table-layout: auto;
        font-size: 11px;
    }

    .erp-print-compact-table th,
    .erp-print-compact-table td {
        padding: 5px 4px;
        line-height: 1.35;
        overflow-wrap: break-word;
    }

    .erp-print-compact-table th {
        white-space: normal;
        text-align: center;
        vertical-align: middle;
    }

    .erp-print-badge {
        display: inline-flex;
        background: #f3f4f6;
        color: #374151;
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .erp-print-positive {
        color: #15803d;
        font-weight: 900;
    }

    .erp-print-negative {
        color: #b91c1c;
        font-weight: 900;
    }

    .erp-print-warning {
        color: #c2410c;
        font-weight: 900;
    }

    .erp-print-neutral {
        color: #374151;
        font-weight: 900;
    }

    .erp-print-text-left {
        text-align: left;
    }

    .erp-print-signatures {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 36px;
        page-break-inside: avoid;
    }

    .erp-print-signature {
        width: 30%;
        border-top: 1px solid #111827;
        text-align: center;
        padding-top: 8px;
        font-weight: 800;
    }

    @media print {
        body {
            background: white;
            padding: 0;
        }

        .erp-print-actions {
            display: none;
        }

        .erp-print-page {
            width: 100%;
            min-height: auto;
            border: none;
            padding: 0;
            margin: 0;
        }

        .erp-print-box,
        .erp-print-summary-card,
        .erp-print-table,
        .erp-print-signatures {
            page-break-inside: avoid;
        }
    }
</style>