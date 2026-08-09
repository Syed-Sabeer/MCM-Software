<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\DocumentStatusController;

Route::controller(DocumentStatusController::class)->prefix('document-statuses')->group(function () {
    Route::get('{statusType}', 'index')->name('admin.document_statuses.index');
    Route::post('{statusType}', 'store')->name('admin.document_statuses.store');
    Route::put('{statusType}/{id}', 'update')->name('admin.document_statuses.update');
    Route::delete('{statusType}/{id}', 'destroy')->name('admin.document_statuses.delete');
});
