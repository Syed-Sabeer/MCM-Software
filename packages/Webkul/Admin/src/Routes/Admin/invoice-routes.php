<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\CustomerPortal\VisibilityController;
use Webkul\Admin\Http\Controllers\Quote\InvoiceController;

Route::controller(InvoiceController::class)->prefix('invoices')->group(function () {
    Route::get('', 'index')->name('admin.invoices.index');
    Route::post('from-proforma/{proformaId}', 'store')->name('admin.invoices.store');
    Route::get('view/{id}', 'view')->name('admin.invoices.view');
    Route::get('print/{id}', 'print')->name('admin.invoices.print');
    Route::post('{id}/status', 'changeStatus')->name('admin.invoices.status');
    Route::post('{id}/customer-visibility', [VisibilityController::class, 'invoice'])->name('admin.invoices.customer_visibility');
    Route::post('{id}/receipts', 'storeReceipt')->name('admin.invoices.receipts.store');
    Route::delete('{id}/receipts/{receiptId}', 'deleteReceipt')->name('admin.invoices.receipts.delete');
});
