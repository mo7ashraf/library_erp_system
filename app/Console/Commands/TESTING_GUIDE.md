# Library ERP System — Testing Guide

This guide contains the development checks used to validate inventory and finance processing.

## 1. Clear cache before testing

```powershell
php artisan optimize:clear
php artisan inventory:check-flow
php artisan finance:check-flow
php artisan optimize:clear
php artisan list | findstr check


---

# 5. Run both test files

After adding the files:

```powershell
php artisan optimize:clear
php artisan inventory:check-flow
php artisan finance:check-flow


---

# 5. Run the full checks

Run:

```powershell
php artisan optimize:clear
php artisan list | findstr check
php artisan erp:check-all

## 7. Run all checks together

After adding all testing commands, run:

```powershell
php artisan optimize:clear
php artisan erp:check-all

php artisan erp:check-ledgers

## Financial summary integrity check

Run:

```powershell
php artisan erp:check-financial-summary

## ERP dashboard integrity check

Run:

```powershell
php artisan erp:check-dashboard

php artisan erp:check-sales-report

php artisan erp:check-purchase-report
php artisan erp:check-all

php artisan erp:check-inventory-report