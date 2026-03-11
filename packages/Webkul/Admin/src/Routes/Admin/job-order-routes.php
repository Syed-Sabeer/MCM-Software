<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\PurchaseOrder\JobOrderController;

Route::controller(JobOrderController::class)->prefix('job-orders')->group(function () {
    Route::get('', 'index')->name('admin.job_orders.index');
    Route::get('create', 'create')->name('admin.job_orders.create');
    Route::post('create', 'store')->name('admin.job_orders.store');
    Route::get('view/{id}', 'view')->name('admin.job_orders.view');
    Route::get('edit/{id}', 'edit')->name('admin.job_orders.edit');
    Route::put('edit/{id}', 'update')->name('admin.job_orders.update');
    Route::delete('{id}', 'destroy')->name('admin.job_orders.delete');
    Route::post('mass-destroy', 'massDestroy')->name('admin.job_orders.mass_delete');
});
