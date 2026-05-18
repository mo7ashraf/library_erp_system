<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesInvoiceReceiptController;
use App\Http\Controllers\StockTransferReceiptController;
use App\Http\Controllers\SalesReturnReceiptController;
use App\Http\Controllers\PurchaseReturnReceiptController;
use App\Http\Controllers\StockCountReceiptController;
use App\Http\Controllers\DamagedStockReceiptController;
use App\Http\Controllers\ReceiptVoucherPrintController;
use App\Http\Controllers\PaymentVoucherPrintController;

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

        Route::get('/damaged-stock-documents/{damagedStockDocument}/receipt', [DamagedStockReceiptController::class, 'show'])
            ->name('damaged-stock-documents.receipt');

        Route::get('/receipt-vouchers/{receiptVoucher}/receipt', [ReceiptVoucherPrintController::class, 'show'])
            ->name('receipt-vouchers.receipt');

        Route::get('/payment-vouchers/{paymentVoucher}/receipt', [PaymentVoucherPrintController::class, 'show'])
            ->name('payment-vouchers.receipt');
    });

Route::get('/', function () {
    return view('welcome');
});
