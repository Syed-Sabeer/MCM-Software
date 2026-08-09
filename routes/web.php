<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Webkul\Admin\Http\Controllers\DocumentStatusController;
use Webkul\Admin\Http\Middleware\Bouncer as AdminBouncer;
use Webkul\Admin\Http\Middleware\Locale as AdminLocale;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/nd', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');

    return "<h2>All caches cleared successfully!</h2>";
});

Route::middleware(['web', AdminLocale::class, AdminBouncer::class])
    ->prefix(config('app.admin_path'))
    ->group(function () {
        Route::controller(DocumentStatusController::class)->prefix('document-statuses')->group(function () {
            Route::get('{statusType}', 'index')->name('admin.document_statuses.index');
            Route::post('{statusType}', 'store')->name('admin.document_statuses.store');
            Route::put('{statusType}/{id}', 'update')->name('admin.document_statuses.update');
            Route::delete('{statusType}/{id}', 'destroy')->name('admin.document_statuses.delete');
        });
    });
