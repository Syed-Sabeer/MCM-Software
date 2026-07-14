<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\CustomerPortal\VisibilityController;
use Webkul\Admin\Http\Controllers\PurchaseOrder\JobOrderController;

Route::post('job-orders/{id}/customer-visibility', [VisibilityController::class, 'jobOrder'])->name('admin.job_orders.customer_visibility');

Route::controller(JobOrderController::class)->prefix('job-orders')->group(function () {
    Route::get('', 'index')->name('admin.job_orders.index');
    Route::get('create', 'create')->name('admin.job_orders.create');
    Route::post('create', 'store')->name('admin.job_orders.store');
    Route::get('view/{id}', 'view')->name('admin.job_orders.view');
    Route::get('{id}/job-card/pdf', 'downloadJobCardPdf')->name('admin.job_orders.job_card.pdf');
    Route::get('{id}/job-card/csv', 'downloadJobCardCsv')->name('admin.job_orders.job_card.csv');
    Route::get('{id}/requirement-sheet/pdf', 'downloadRequirementSheetPdf')->name('admin.job_orders.requirement_sheet.pdf');
    Route::get('{id}/requirement-sheet/csv', 'downloadRequirementSheetCsv')->name('admin.job_orders.requirement_sheet.csv');
    Route::get('edit/{id}', 'edit')->name('admin.job_orders.edit');
    Route::put('edit/{id}', 'update')->name('admin.job_orders.update');
    Route::delete('{id}', 'destroy')->name('admin.job_orders.delete');
    Route::post('mass-destroy', 'massDestroy')->name('admin.job_orders.mass_delete');
});
