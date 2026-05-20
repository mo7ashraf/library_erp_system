# Library ERP V1 Release Plan

This document defines the remaining work before considering the system ready for version 1.0.

## 1. Current Status

The system currently includes:

- Core master data.
- Sales invoices.
- Sales returns.
- Purchase invoices.
- Purchase returns.
- Stock transfers.
- Stock count documents.
- Damaged stock documents.
- Receipt vouchers.
- Payment vouchers.
- Customer ledgers.
- Supplier ledgers.
- Reports.
- Print views.
- Integrity check commands.
- Release readiness check.

## 2. Main Goal for V1

Version 1.0 should be a stable operational ERP version for managing:

- Library inventory.
- Sales and returns.
- Purchases and returns.
- Stock movements.
- Cashbox and bank transactions.
- Customer and supplier balances.
- Operational and financial reports.

## 3. V1 Must-Have Checklist

### Application Stability

- No fatal errors.
- `php artisan route:list` works.
- `php artisan optimize:clear` works.
- Admin panel opens.
- Login works.
- Sidebar navigation is readable.

### Processing Stability

The following commands should pass:

```powershell
php artisan inventory:check-flow
php artisan finance:check-flow
php artisan erp:check-documents
php artisan erp:check-ledgers
php artisan erp:check-financial-summary
php artisan erp:check-dashboard
php artisan erp:check-sales-report
php artisan erp:check-purchase-report
php artisan erp:check-inventory-report
php artisan erp:check-party-balance-reports
php artisan erp:check-posted-documents-report
php artisan erp:check-release-readiness

Full Check
php artisan erp:check-all
php artisan erp:check-release-readiness --run-checks
``` 
## 4. V1 Functional Tests
Sales
Create sales invoice.
Add more than one item.
Confirm quantity does not reset.
Post invoice.
Confirm stock decreases.
Print invoice.
Create sales return.
Confirm return quantity cannot exceed sold quantity.
Print sales return.
Check sales report.
Purchases
Create purchase invoice.
Add more than one item.
Post invoice.
Confirm stock increases.
Create purchase return.
Confirm return quantity cannot exceed purchased quantity.
Print purchase return.
Check purchase report.
Inventory
Create stock transfer.
Confirm source warehouse decreases.
Confirm destination warehouse increases.
Print transfer.
Create stock count document.
Confirm increase/decrease movements.
Create damaged stock document.
Confirm stock decreases.
Check inventory report.
Finance
Create receipt voucher.
Confirm cashbox/bank increases.
Create payment voucher.
Confirm cashbox/bank decreases.
Confirm over-payment is rejected.
Check financial summary report.
Check customer ledger.
Check supplier ledger.
Check customer/supplier balance reports.
Audit
Check posted documents audit report.
Filter by all.
Filter by posted.
Filter by draft.
Print audit report.
## 5. UI Polish Before V1

Recommended before tagging V1:

Extract repeated report CSS into shared components.
Standardize all report headers.
Standardize all print views.
Add application logo.
Review sidebar grouping.
Review Arabic labels.
Review empty states.
Review mobile/tablet layout where applicable.
## 6. Security Before V1
Review roles and permissions.
Restrict document posting to authorized roles.
Restrict financial reports to authorized roles.
Restrict print actions if needed.
Review admin user creation flow.
Ensure production APP_DEBUG=false.
## 7. Data Quality Before V1
Add realistic seed data.
Add default branch.
Add default warehouse.
Add default cashbox.
Add default bank account if needed.
Add default admin user creation instructions.
## 8. Deployment Before V1
Prepare production .env.
Use MySQL for production unless SQLite is intentional.
Run migrations.
Run seeders.
Run release readiness command.
Confirm backups.
Confirm storage permissions.
## 9. Recommended V1 Tagging Process

After all checks pass:

git status
git add .
git commit -m "Prepare version 1 release"
git tag v1.0.0
git push origin main
git push origin v1.0.0
## 10. Known Risk Areas
Old test data can cause integrity check failures.
Repeated CSS can make UI changes slower.
Print views need browser print testing.
Large reports may need pagination or export later.
Role permissions should be reviewed before real production use.
## 11. Recommended After V1
Excel export for reports.
PDF generation.
Shared report components.
Better dashboard charts.
More granular permissions.
Activity log.
Backup command.
Database seeders and factories.