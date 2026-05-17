<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Activity\ActivityController;

Route::controller(ActivityController::class)->prefix('activities')->group(function () {
    Route::get('', 'index')->name('admin.activities.index');

    Route::get('calendar', 'calendar')->name('admin.activities.calendar');

    Route::get('my-tasks', 'myTasks')->name('admin.activities.my_tasks');

    Route::get('my-tasks/get', 'myTasksData')->name('admin.activities.my_tasks_data');

    Route::get('my-tasks/summary', 'myTasksSummary')->name('admin.activities.my_tasks_summary');

    Route::get('get', 'get')->name('admin.activities.get');

    Route::get('search-organizations', 'searchOrganizations')->name('admin.activities.search_organizations');

    Route::get('search-persons', 'searchPersons')->name('admin.activities.search_persons');

    Route::get('search-employee-users', 'searchEmployeeUsers')->name('admin.activities.search_employee_users');

    Route::post('create', 'store')->name('admin.activities.store');

    Route::get('edit/{id}', 'edit')->name('admin.activities.edit');

    Route::put('edit/{id}', 'update')->name('admin.activities.update');

    Route::get('preview/{id}', 'preview')->name('admin.activities.file_preview');

    Route::get('download/{id}', 'download')->name('admin.activities.file_download');

    Route::delete('{id}', 'destroy')->name('admin.activities.delete');

    Route::post('mass-update', 'massUpdate')->name('admin.activities.mass_update');

    Route::post('mass-destroy', 'massDestroy')->name('admin.activities.mass_delete');
});
