# Deployment Guide

This guide explains how to deploy the Library ERP System to a server.

## 1. Server Requirements

- PHP 8.3+
- Composer
- MySQL or SQLite
- Web server: Nginx or Apache
- Required PHP extensions:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - Zip

## 2. Clone Project

```bash
git clone https://github.com/mo7ashraf/library_erp_system.git
cd library_erp_system
```

## 3. Install Dependencies
composer install --no-dev --optimize-autoloader

## 4. Environment File
cp .env.example .env
php artisan key:generate

### .env production
APP_NAME="Library ERP System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_erp_system
DB_USERNAME=your_user
DB_PASSWORD=your_password

## 5. Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

## 6. Database Migration
php artisan migrate --force

### If seeders are required:
php artisan db:seed --force

## 7. Optimize Laravel
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

## 8. Web Server Document Root
Set the web server document root to:
/path/to/library_erp_system/public
Never expose the project root directly.

## 9. Nginx Example
server {
    listen 80;
    server_name your-domain.com;

    root /var/www/library_erp_system/public;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

## 10. Post-Deployment Checks
php artisan optimize:clear
php artisan route:list
php artisan erp:check-release-readiness
php artisan erp:check-release-readiness --run-checks

/admin
/admin/erp-dashboard
/admin/financial-summary-report
/admin/sales-summary-report
/admin/purchase-summary-report
/admin/inventory-summary-report
/admin/customer-balance-report
/admin/supplier-balance-report
/admin/posted-documents-report

## 11. Print Checks
/admin/prints/financial-summary-report
/admin/prints/sales-summary-report
/admin/prints/purchase-summary-report
/admin/prints/inventory-summary-report
/admin/prints/customer-balance-report
/admin/prints/supplier-balance-report
/admin/prints/posted-documents-report

## 12. Common Issues
### Route or class fatal error

php artisan optimize:clear
composer dump-autoload
php artisan route:list
View not found

Check that the Blade file exists in:

resources/views/prints
resources/views/filament/pages

Then run:

php artisan view:clear
Wrong class name in controller

Make sure the class name matches the file name.

Example:

StockTransferReceiptController.php

Must contain:

class StockTransferReceiptController extends Controller
Cache issue after deployment

Run:

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache