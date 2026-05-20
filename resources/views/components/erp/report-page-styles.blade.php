<style>
    .erp-report-page {
        direction: rtl;
        font-family: "Cairo", Tahoma, Arial, sans-serif;
        color: #111827;
    }

    .erp-report-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        margin-bottom: 18px;
        overflow: hidden;
    }

    .erp-report-card-body {
        padding: 18px;
    }

    .erp-report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid #f1f5f9;
        padding: 18px;
    }

    .erp-report-title {
        margin: 0;
        font-size: 24px;
        font-weight: 900;
        color: #111827;
        line-height: 1.4;
    }

    .erp-report-subtitle {
        margin-top: 4px;
        color: #6b7280;
        font-size: 14px;
        font-weight: 600;
    }

    .erp-report-filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    .erp-report-filter-grid-4 {
        grid-template-columns: 1fr 1fr 1fr auto;
    }

    .erp-report-field label {
        display: block;
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: 900;
        color: #374151;
    }

    .erp-report-field input,
    .erp-report-field select {
        width: 100%;
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        background: #ffffff;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        padding: 0 12px;
        outline: none;
    }

    .erp-report-field input:focus,
    .erp-report-field select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.16);
    }

    .erp-report-actions {
        display: flex;
        gap: 8px;
    }

    .erp-report-btn {
        height: 42px;
        border: none;
        border-radius: 12px;
        padding: 0 16px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 900;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .erp-report-btn-primary {
        background: #f59e0b;
        color: #111827;
    }

    .erp-report-btn-secondary {
        background: #6b7280;
        color: #ffffff;
    }

    .erp-report-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .erp-report-kpi {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .erp-report-kpi-title {
        font-size: 13px;
        font-weight: 900;
        color: #6b7280;
    }

    .erp-report-kpi-value {
        margin-top: 10px;
        font-size: 22px;
        font-weight: 900;
        line-height: 1.3;
        color: #111827;
    }

    .erp-report-kpi-note {
        margin-top: 8px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 800;
    }

    .erp-report-kpi-green {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .erp-report-kpi-green .erp-report-kpi-title,
    .erp-report-kpi-green .erp-report-kpi-value {
        color: #15803d;
    }

    .erp-report-kpi-red {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .erp-report-kpi-red .erp-report-kpi-title,
    .erp-report-kpi-red .erp-report-kpi-value {
        color: #b91c1c;
    }

    .erp-report-kpi-amber {
        background: #fffbeb;
        border-color: #fde68a;
    }

    .erp-report-two-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    .erp-report-table-header {
        padding: 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .erp-report-table-title {
        font-size: 18px;
        font-weight: 900;
        margin: 0;
        color: #111827;
    }

    .erp-report-table-wrapper {
        overflow-x: auto;
    }

    .erp-report-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        font-size: 14px;
    }

    .erp-report-table th {
        background: #f9fafb;
        color: #374151;
        font-size: 13px;
        font-weight: 900;
        text-align: right;
        padding: 13px 14px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .erp-report-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .erp-report-table tbody tr:hover {
        background: #fffbeb;
    }

    .erp-report-table .text-left {
        text-align: left;
    }

    .erp-report-badge {
        display: inline-flex;
        background: #f3f4f6;
        color: #374151;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .erp-report-positive {
        color: #15803d;
        font-weight: 900;
    }

    .erp-report-negative {
        color: #b91c1c;
        font-weight: 900;
    }

    .erp-report-warning {
        color: #c2410c;
        font-weight: 900;
    }

    .erp-report-neutral {
        color: #374151;
        font-weight: 900;
    }

    .erp-report-link {
        color: #2563eb;
        font-weight: 900;
        text-decoration: none;
    }

    .erp-report-link:hover {
        text-decoration: underline;
    }

    .erp-report-empty {
        text-align: center;
        padding: 28px 12px;
        color: #6b7280;
        font-weight: 800;
    }

    @media (max-width: 1100px) {
        .erp-report-filter-grid,
        .erp-report-filter-grid-4,
        .erp-report-kpi-grid,
        .erp-report-two-grid {
            grid-template-columns: 1fr;
        }

        .erp-report-actions {
            width: 100%;
        }

        .erp-report-btn {
            width: 100%;
        }
    }
</style>