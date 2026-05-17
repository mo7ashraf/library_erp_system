<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesInvoiceReceiptController;
use App\Http\Controllers\StockTransferReceiptController;
use App\Http\Controllers\SalesReturnReceiptController;
use App\Http\Controllers\PurchaseReturnReceiptController;
use App\Http\Controllers\StockCountReceiptController;

Route::middleware(['auth'])
    ->prefix('admin/prints')
    ->name('admin.prints.')
    ->group(function () {
        Route::get('/sales-invoices/{salesInvoice}/receipt', [SalesInvoiceReceiptController::class, 'show'])
            ->name('sales-invoices.receipt');
        
        Route::get('/stock-transfers/{stockTransfer}/receipt', [StockTransferReceiptController::class, 'show'])
            ->name('stock-transfers.receipt');
        
        Route::get('/sales-returns/{salesReturn}/receipt', [SalesReturnReceiptController::class, 'show'])
            ->name('sales-returns.receipt');

        Route::get('/purchase-returns/{purchaseReturn}/receipt', [PurchaseReturnReceiptController::class, 'show'])
            ->name('purchase-returns.receipt');
        
        Route::get('/stock-count-documents/{stockCountDocument}/receipt', [StockCountReceiptController::class, 'show'])
            ->name('stock-count-documents.receipt');
    });

Route::get('/', function () {
    return view('welcome');
});
