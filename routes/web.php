<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesInvoiceReceiptController;

Route::middleware(['auth'])
    ->prefix('admin/prints')
    ->name('admin.prints.')
    ->group(function () {
        Route::get('/sales-invoices/{salesInvoice}/receipt', [SalesInvoiceReceiptController::class, 'show'])
            ->name('sales-invoices.receipt');
    });

Route::get('/', function () {
    return view('welcome');
});
