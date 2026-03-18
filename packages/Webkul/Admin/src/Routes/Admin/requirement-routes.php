<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\PurchaseOrder\RequirementController;

Route::controller(RequirementController::class)->prefix('requirements')->group(function () {
    Route::get('', 'index')->name('admin.requirements.index');
    Route::delete('{id}', 'destroy')->name('admin.requirements.delete');
    Route::post('mass-destroy', 'massDestroy')->name('admin.requirements.mass_delete');
});
