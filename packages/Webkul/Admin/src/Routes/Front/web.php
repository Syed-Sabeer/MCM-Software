<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\CustomerPortal\PortalController;
use Webkul\Admin\Http\Controllers\Controller;

/**
 * Home routes.
 */
Route::get('/', [Controller::class, 'redirectToLogin'])->name('krayin.home');

Route::prefix('customer-portal')->middleware('customer_portal')->group(function () {
    Route::get('', [PortalController::class, 'dashboard'])->name('customer_portal.dashboard');

    Route::get('quotes', [PortalController::class, 'quotes'])->name('customer_portal.quotes.index');
    Route::get('quotes/{id}', [PortalController::class, 'quote'])->name('customer_portal.quotes.view');

    Route::get('proformas', [PortalController::class, 'proformas'])->name('customer_portal.proformas.index');
    Route::get('proformas/{id}', [PortalController::class, 'proforma'])->name('customer_portal.proformas.view');

    Route::get('job-orders', [PortalController::class, 'jobOrders'])->name('customer_portal.job_orders.index');
    Route::get('job-orders/{id}', [PortalController::class, 'jobOrder'])->name('customer_portal.job_orders.view');

    Route::get('products', [PortalController::class, 'products'])->name('customer_portal.products.index');
    Route::get('products/{id}', [PortalController::class, 'product'])->name('customer_portal.products.view');

    Route::delete('logout', [PortalController::class, 'logout'])->name('customer_portal.logout');
});
