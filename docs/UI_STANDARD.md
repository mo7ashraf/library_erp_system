# Library ERP UI Standard

This document defines the visual and UX standard for the Library ERP System.

## 1. General Direction

The system UI should be:

- Arabic-first.
- RTL by default.
- Clear for non-technical users.
- Consistent across all resources, reports, ledgers, and print views.
- Optimized for operational work, not decoration.

## 2. Typography

Use Cairo font for:

- Filament custom pages.
- Print views.
- Receipts.
- Reports.

Fallback:

```css
font-family: "Cairo", Tahoma, Arial, sans-serif;
```

## 3. Page Layout Standard

Every custom report page should contain:

Header card.
Short description.
Filter card.
KPI cards.
Summary tables.
Detailed tables.
Print button when applicable.

Recommended structure:

Page Header
Filters
KPI Cards
Summary Tables
Details Table

## 4. Report Naming Standard

Use clear Arabic names:

لوحة التحكم التنفيذية
الملخص المالي
تقرير المبيعات
تقرير المشتريات
تقرير المخزون
أرصدة العملاء
أرصدة الموردين
مراجعة المستندات
## 5. KPI Cards

KPI cards should use the following style:

White background.
Rounded corners.
Light border.
Soft shadow.
Clear title.
Large numeric value.
Use green for positive values.
Use red for negative/outgoing values.
Use amber/orange for warnings.
Use neutral gray for informational values.
## 6. Table Standard

All report tables should include:

Column "م" for row numbering.
Numbering starts from 1 using $loop->iteration.
Header row with light gray background.
Right-aligned Arabic text.
Left-aligned numeric values.
Empty state row when there is no data.
Hover effect in screen views.
No hover effect required in print views.
## 7. Table Numbering

Correct:

{{ $loop->iteration }}

Do not use zero-based numbering.

## 8. Money Format

Use:

$money = fn ($value) => number_format((float) $value, 2) . ' ج.م';
## 9. Quantity Format

Use:

$number = fn ($value) => number_format((float) $value, 3);
## 10. Print View Standard

All print views should include:

A4 portrait for normal receipts.
A4 landscape for wide reports.
Cairo font.
Centered title.
Report metadata box.
Period/filter box if report-based.
Signature section.
Print and close buttons hidden during printing.
## 11. Wide Print Tables

For reports with many columns, use A4 landscape and compact table styles.

Examples:

Purchase summary report.
Inventory summary report.
Posted documents audit report.
Customer/supplier balance report.
## 12. Print Signature Standard

Use 3 signatures when applicable:

المحاسب
المراجع
اعتماد المسؤول

For inventory reports:

أمين المخزن
المراجع
اعتماد المسؤول
13. Button Standard

Main action:

عرض التقرير

Secondary actions:

طباعة
مسح
إغلاق
## 14. Color Meaning
Color	Meaning
Green	Positive / inflow / debit balance / stock value
Red	Outflow / cost / negative / credit balance
Amber	Warning / draft / low stock
Gray	Neutral / informational
## 15. Current Issue

Many custom report pages currently duplicate CSS.

This is acceptable for the current development phase, but should be refactored before version 1.0.

## 16. Recommended Refactor Before V1

Create shared Blade partials/components for:

resources/views/components/erp/report-layout.blade.php
resources/views/components/erp/kpi-card.blade.php
resources/views/components/erp/report-table.blade.php
resources/views/components/erp/print-layout.blade.php

Then gradually migrate report pages to these shared components.

## 17. Do Not Break Existing Reports

Any UI refactor must preserve:

Existing route names.
Existing print route names.
Existing report service outputs.
Existing integrity check commands.
Existing table columns unless intentionally improved.
## 18. Minimum UI Acceptance Criteria

Before release:

All report pages open without errors.
All print pages open without errors.
Wide tables fit in print layout.
All report pages follow the same visual structure.
Arabic text is readable.
Filters work.
Print button opens the correct print page.
Numbering starts from 1.