# Library ERP Final Testing Checklist

## 1. Environment

Run:

```powershell
php artisan optimize:clear
php artisan migrate
php artisan route:list
```
## Core Processing Checks

php artisan inventory:check-flow
php artisan finance:check-flow
php artisan erp:check-documents
php artisan erp:check-ledgers
php artisan erp:check-all