# UI Refactor Progress

This document tracks the gradual UI cleanup for the Library ERP System.

## Goal

Reduce repeated CSS across report and print views without breaking existing pages.

## Current Strategy

The refactor will be done gradually:

1. Add shared style components.
2. Keep old pages working.
3. Migrate one page at a time.
4. Test each page after migration.
5. Avoid changing service logic during UI refactor.

## Shared Components Added

```text
resources/views/components/erp/report-page-styles.blade.php
resources/views/components/erp/print-page-styles.blade.php