<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\PurchaseOrder\VendorQuoteController;

Route::controller(VendorQuoteController::class)->prefix('vendor-quotes')->group(function () {
    Route::get('', 'index')->name('admin.vendor_quotes.index');
    Route::get('create', 'create')->name('admin.vendor_quotes.create');
    Route::post('create', 'store')->name('admin.vendor_quotes.store');
    Route::get('view/{id}', 'view')->name('admin.vendor_quotes.view');
    Route::get('print/{id}', 'print')->name('admin.vendor_quotes.print');
    Route::get('edit/{id}', 'edit')->name('admin.vendor_quotes.edit');
    Route::put('edit/{id}', 'update')->name('admin.vendor_quotes.update');
    Route::delete('{id}', 'destroy')->name('admin.vendor_quotes.delete');
    Route::post('mass-destroy', 'massDestroy')->name('admin.vendor_quotes.mass_delete');
});
