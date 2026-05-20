# Library ERP System

A Laravel-based ERP system for managing library operations, inventory, sales, purchases, treasury transactions, reporting, and printable documents.

## Technology Stack

- PHP 8.3+
- Laravel 13
- Filament 4
- SQLite / MySQL compatible
- Spatie Laravel Permission
- Blade print views
- Arabic RTL interface
- Cairo font for readable Arabic reports and receipts

## Main Modules

### Master Data

- Branches
- Warehouses
- Items
- Units
- Customers
- Suppliers
- Cashboxes
- Bank accounts

### Inventory

- Warehouse item balances
- Stock movements
- Stock transfers
- Stock count documents
- Damaged stock documents
- Inventory processing checks

### Sales

- Sales invoices
- Sales returns
- Return quantity validation
- Printable sales invoice receipts
- Printable sales return receipts
- Sales summary report

### Purchases

- Purchase invoices
- Purchase returns
- Return quantity validation
- Printable purchase return receipts
- Purchase summary report

### Finance

- Receipt vouchers
- Payment vouchers
- Cashbox and bank processing
- Treasury transactions
- Customer ledgers
- Supplier ledgers
- Financial summary report

### Reports

- Executive ERP dashboard
- Financial summary report
- Sales summary report
- Purchase summary report
- Inventory summary report
- Customer balance report
- Supplier balance report
- Posted documents audit report

### Print Views

The system includes printable A4/A4-landscape views for:

- Sales invoices
- Stock transfers
- Sales returns
- Purchase returns
- Stock count documents
- Damaged stock documents
- Receipt vouchers
- Payment vouchers
- Customer ledger
- Supplier ledger
- Financial summary report
- Sales summary report
- Purchase summary report
- Inventory summary report
- Customer balance report
- Supplier balance report
- Posted documents audit report

## Installation

Clone the repository:

```bash
git clone https://github.com/mo7ashraf/library_erp_system.git
cd library_erp_system
```

## Install PHP dependencies:
composer install

## Create environment file:
cp .env.example .env

## On Windows PowerShell:
Copy-Item .env.example .env

## Generate application key:
php artisan key:generate

## Configure your database in .env.
    
    ### For SQLite:

    DB_CONNECTION=sqlite
    DB_DATABASE=database/database.sqlite

## Create the SQLite file if needed:
touch database/database.sqlite

## On Windows PowerShell:
New-Item database/database.sqlite -ItemType File

## Run migrations:
php artisan migrate

## Run seeders if available:
php artisan db:seed

## Clear caches:
php artisan optimize:clear

## Start the development server:
php artisan serve

## Open
http://127.0.0.1:8000/admin

## Useful Artisan Checks

### Run individual checks:
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

### Run all ERP checks:
php artisan erp:check-all

### Run release readiness checks:
php artisan erp:check-release-readiness
php artisan erp:check-release-readiness --run-checks

## Important Report URLs
/admin/erp-dashboard
/admin/financial-summary-report
/admin/sales-summary-report
/admin/purchase-summary-report
/admin/inventory-summary-report
/admin/customer-balance-report
/admin/supplier-balance-report
/admin/posted-documents-report

## Important Print URLs
/admin/prints/financial-summary-report
/admin/prints/sales-summary-report
/admin/prints/purchase-summary-report
/admin/prints/inventory-summary-report
/admin/prints/customer-balance-report
/admin/prints/supplier-balance-report
/admin/prints/posted-documents-report

## Development Notes
Keep all document-processing logic consistent with inventory and finance services.
Do not bypass posting logic by manually changing balances.
All printable views should use Cairo font and RTL layout.
Numbering in report tables starts from 1.
Wide print reports should use A4 landscape.
Every major report should have an integrity check command.

## Before pushing a release, run:
php artisan optimize:clear
php artisan route:list
php artisan erp:check-release-readiness --run-checks


---

# 3. Optional README update

في آخر `README.md` أضف تحت قسم `Final Testing`:

```md
## Release Planning

See:

```text
docs/UI_STANDARD.md
docs/V1_RELEASE_PLAN.md
docs/RELEASE_NOTES.md

---

# 4. Run checks

نفّذ:

```powershell
php artisan optimize:clear
php artisan route:list
php artisan erp:check-release-readiness
```
ولو كله تمام:
php artisan erp:check-release-readiness --run-checks
