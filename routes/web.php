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
use App\Http\Controllers\CustomerLedgerPrintController;
use App\Http\Controllers\SupplierLedgerPrintController;
use App\Http\Controllers\FinancialSummaryPrintController;
use App\Http\Controllers\SalesSummaryPrintController;
use App\Http\Controllers\PurchaseSummaryPrintController;

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
        
         Route::get('/customer-ledger', [CustomerLedgerPrintController::class, 'show'])
            ->name('customer-ledger');

        Route::get('/supplier-ledger', [SupplierLedgerPrintController::class, 'show'])
            ->name('supplier-ledger');

        Route::get('/financial-summary-report', [FinancialSummaryPrintController::class, 'show'])
            ->name('financial-summary-report');
        
        Route::get('/sales-summary-report', [SalesSummaryPrintController::class, 'show'])
            ->name('sales-summary-report');

        Route::get('/purchase-summary-report', [PurchaseSummaryPrintController::class, 'show'])
            ->name('purchase-summary-report');
    });

Route::get('/', function () {
    return view('welcome');
});
