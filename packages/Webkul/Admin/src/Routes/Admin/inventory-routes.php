<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\PurchaseOrder\MaterialInventoryController;

Route::controller(MaterialInventoryController::class)->prefix('inventory')->group(function () {
    Route::get('', 'index')->name('admin.inventory.index');
    Route::get('materials/{materialId}/edit', 'edit')->name('admin.inventory.edit');
    Route::get('materials/{materialId}', 'view')->name('admin.inventory.view');
    Route::post('movements', 'storeMovement')->name('admin.inventory.movements.store');
    Route::put('materials/{materialId}/settings', 'updateSettings')->name('admin.inventory.settings.update');
});
