<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\User\ForgotPasswordController;
use Webkul\Admin\Http\Controllers\User\ResetPasswordController;
use Webkul\Admin\Http\Controllers\User\SessionController;

Route::withoutMiddleware(['user'])->group(function () {
    /**
     * Redirect route.
     */
    Route::get('/', [Controller::class, 'redirectToLogin']);

    /**
     * Session routes.
     */
    Route::controller(SessionController::class)->group(function () {
        Route::prefix('login')->group(function () {
            Route::get('', 'create')->name('admin.session.create');

            Route::post('', 'store')->middleware('throttle:5,1')->name('admin.session.store');
        });

        Route::middleware(['user'])->group(function () {
            Route::delete('logout', 'destroy')->name('admin.session.destroy');
        });
    });

    /**
     * Forgot password routes.
     */
    Route::controller(ForgotPasswordController::class)->prefix('forget-password')->group(function () {
        Route::get('', 'create')->name('admin.forgot_password.create');

        Route::post('', 'store')->middleware('throttle:3,1')->name('admin.forgot_password.store');

        Route::get('verify', 'verifyForm')->name('admin.forgot_password.verify');

        Route::post('verify', 'verify')->middleware('throttle:8,1')->name('admin.forgot_password.verify.store');

        Route::post('resend', 'resend')->middleware('throttle:2,1')->name('admin.forgot_password.resend');
    });

    /**
     * Reset password routes.
     */
    Route::controller(ResetPasswordController::class)->prefix('reset-password')->group(function () {
        Route::get('', 'create')->name('admin.reset_password.create');

        Route::post('', 'store')->middleware('throttle:5,1')->name('admin.reset_password.store');

        Route::get('{token}', fn () => redirect()->route('admin.forgot_password.create'));
    });
});
