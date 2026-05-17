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