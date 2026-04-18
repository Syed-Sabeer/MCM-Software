<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Search\GlobalSearchController;

Route::controller(GlobalSearchController::class)->prefix('search')->group(function () {
    Route::get('global', 'search')->name('admin.search.global');
});
