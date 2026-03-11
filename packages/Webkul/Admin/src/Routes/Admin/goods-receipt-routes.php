<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\PurchaseOrder\GoodsReceiptController;

Route::controller(GoodsReceiptController::class)->prefix('goods-receipts')->group(function () {
    Route::get('', 'index')->name('admin.goods_receipts.index');
    Route::get('create', 'create')->name('admin.goods_receipts.create');
    Route::post('create', 'store')->name('admin.goods_receipts.store');
    Route::get('view/{id}', 'view')->name('admin.goods_receipts.view');
    Route::get('edit/{id}', 'edit')->name('admin.goods_receipts.edit');
    Route::put('edit/{id}', 'update')->name('admin.goods_receipts.update');
    Route::delete('{id}', 'destroy')->name('admin.goods_receipts.delete');
});
