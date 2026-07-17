<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\CustomerPortal\AuthController;
use Webkul\Admin\Http\Controllers\CustomerPortal\PortalController;
use Webkul\Admin\Http\Controllers\User\ForgotPasswordController as SharedForgotPasswordController;

/**
 * Home routes.
 */
Route::get('/', [Controller::class, 'redirectToLogin'])->name('krayin.home');

Route::prefix('customer-portal')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', fn () => redirect()->route('admin.session.create'))->name('customer_portal.login');
        Route::post('login', fn () => redirect()->route('admin.session.create'))->name('customer_portal.login.store');
        Route::get('forgot-password', fn () => redirect()->route('admin.forgot_password.create'))->name('customer_portal.password.request');
        Route::post('forgot-password', [SharedForgotPasswordController::class, 'store'])->middleware('throttle:3,1')->name('customer_portal.password.email');
        Route::get('reset-password/{token}', fn () => redirect()->route('admin.forgot_password.create'))->name('customer_portal.password.reset');
        Route::post('reset-password', fn () => redirect()->route('admin.forgot_password.create'))->name('customer_portal.password.update');
        Route::get('invitation/{token}', [AuthController::class, 'invitationForm'])->name('customer_portal.invitation.show');
        Route::post('invitation/{token}', [AuthController::class, 'acceptInvitation'])->middleware('throttle:5,1')->name('customer_portal.invitation.accept');
    });

    Route::middleware('customer_portal')->group(function () {
        Route::get('', [PortalController::class, 'dashboard'])->name('customer_portal.dashboard');

        Route::get('quotes', [PortalController::class, 'quotes'])->name('customer_portal.quotes.index');
        Route::get('quotes/{id}', [PortalController::class, 'quote'])->name('customer_portal.quotes.view');
        Route::get('quotes/{id}/pdf', [PortalController::class, 'quotePdf'])->name('customer_portal.quotes.pdf');
        Route::get('quotes/{id}/attachment', [PortalController::class, 'quoteAttachment'])->name('customer_portal.quotes.attachment');

        Route::get('proformas', [PortalController::class, 'proformas'])->name('customer_portal.proformas.index');
        Route::get('proformas/{id}', [PortalController::class, 'proforma'])->name('customer_portal.proformas.view');
        Route::get('proformas/{id}/pdf', [PortalController::class, 'proformaPdf'])->name('customer_portal.proformas.pdf');
        Route::get('proformas/{id}/attachment', [PortalController::class, 'proformaAttachment'])->name('customer_portal.proformas.attachment');

        Route::get('invoices', [PortalController::class, 'invoices'])->name('customer_portal.invoices.index');
        Route::get('invoices/{id}', [PortalController::class, 'invoice'])->name('customer_portal.invoices.view');
        Route::get('invoices/{id}/pdf', [PortalController::class, 'invoicePdf'])->name('customer_portal.invoices.pdf');

        Route::get('job-orders', [PortalController::class, 'jobOrders'])->name('customer_portal.job_orders.index');
        Route::get('job-orders/{id}', [PortalController::class, 'jobOrder'])->name('customer_portal.job_orders.view');

        Route::get('products', [PortalController::class, 'products'])->name('customer_portal.products.index');
        Route::get('products/{id}', [PortalController::class, 'product'])->name('customer_portal.products.view');

        Route::get('company', [PortalController::class, 'company'])->name('customer_portal.company');
        Route::get('contacts', [PortalController::class, 'contacts'])->name('customer_portal.contacts');
        Route::get('contacts/{id}', [PortalController::class, 'contact'])->name('customer_portal.contacts.view');
        Route::get('security', [PortalController::class, 'security'])->name('customer_portal.security');
        Route::put('security', [PortalController::class, 'updateSecurity'])->name('customer_portal.security.update');

        Route::delete('logout', [PortalController::class, 'logout'])->name('customer_portal.logout');
    });
});
