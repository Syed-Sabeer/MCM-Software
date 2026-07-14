<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\CustomerPortal\VisibilityController;
use Webkul\Admin\Http\Controllers\Quote\ProformaInvoiceController;

Route::post('proforma-invoices/{id}/customer-visibility', [VisibilityController::class, 'proforma'])->name('admin.proforma_invoices.customer_visibility');

Route::controller(ProformaInvoiceController::class)->prefix('proforma-invoices')->group(function () {
    Route::get('', 'index')->name('admin.proforma_invoices.index');

    Route::get('create', 'create')->name('admin.proforma_invoices.create');

    Route::post('create', 'store')->name('admin.proforma_invoices.store');

    Route::get('edit/{id}', 'edit')->name('admin.proforma_invoices.edit');

    Route::put('edit/{id}', 'update')->name('admin.proforma_invoices.update');

    Route::get('view/{id}', 'view')->name('admin.proforma_invoices.view');

    Route::get('print/{id}', 'print')->name('admin.proforma_invoices.print');

    Route::post('{id}/status', 'changeStatus')->name('admin.proforma_invoices.status');

    Route::post('{id}/receipts', 'storeReceipt')->name('admin.proforma_invoices.receipts.store');

    Route::delete('{id}/receipts/{receiptId}', 'deleteReceipt')->name('admin.proforma_invoices.receipts.delete');

    Route::delete('{id}', 'destroy')->name('admin.proforma_invoices.delete');

    Route::post('mass-destroy', 'massDestroy')->name('admin.proforma_invoices.mass_delete');
});
