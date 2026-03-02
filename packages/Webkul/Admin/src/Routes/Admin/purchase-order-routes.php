<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\PurchaseOrder\PurchaseOrderController;

Route::controller(PurchaseOrderController::class)->prefix('purchase-orders')->group(function () {
    Route::get('', 'index')->name('admin.purchase_orders.index');

    Route::get('create', 'create')->name('admin.purchase_orders.create');

    Route::post('create', 'store')->name('admin.purchase_orders.store');

    Route::get('edit/{id}', 'edit')->name('admin.purchase_orders.edit');

    Route::put('edit/{id}', 'update')->name('admin.purchase_orders.update');

    Route::get('print/{id}', 'print')->name('admin.purchase_orders.print');

    Route::delete('{id}', 'destroy')->name('admin.purchase_orders.delete');

    Route::post('mass-destroy', 'massDestroy')->name('admin.purchase_orders.mass_delete');
});
