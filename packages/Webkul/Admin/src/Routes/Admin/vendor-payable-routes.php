<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\PurchaseOrder\VendorPayableController;

Route::controller(VendorPayableController::class)->prefix('vendor-payables')->group(function () {
    Route::get('', 'index')->name('admin.vendor_payables.index');
});
