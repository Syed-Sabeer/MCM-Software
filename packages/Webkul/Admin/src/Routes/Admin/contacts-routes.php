<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Contact\ActivityController;
use Webkul\Admin\Http\Controllers\Contact\OrganizationController;
use Webkul\Admin\Http\Controllers\Contact\OrganizationFileController;
use Webkul\Admin\Http\Controllers\Contact\Persons\ActivityController as PersonActivityController;
use Webkul\Admin\Http\Controllers\Contact\Persons\PersonController;
use Webkul\Admin\Http\Controllers\Contact\Persons\TagController;

// Helper function to register contact routes for a given prefix (contacts, customers, vendors)
$registerContactRoutes = function ($prefix) {
    Route::prefix($prefix)->group(function () use ($prefix) {
        /**
         * Persons routes.
         */
        Route::controller(PersonController::class)->prefix('persons')->group(function () use ($prefix) {
            Route::get('', 'index')->name("admin.{$prefix}.persons.index");

            Route::get('create', 'create')->name("admin.{$prefix}.persons.create");

            Route::post('create', 'store')->name("admin.{$prefix}.persons.store");

            Route::get('view/{id}', 'show')->name("admin.{$prefix}.persons.view");

            Route::get('edit/{id}', 'edit')->name("admin.{$prefix}.persons.edit");

            Route::put('edit/{id}', 'update')->name("admin.{$prefix}.persons.update");

            Route::get('search', 'search')->name("admin.{$prefix}.persons.search");

            Route::middleware(['throttle:100,60'])->delete('{id}', 'destroy')->name("admin.{$prefix}.persons.delete");

            Route::post('mass-destroy', 'massDestroy')->name("admin.{$prefix}.persons.mass_delete");

            /**
             * Tag routes.
             */
            Route::controller(TagController::class)->prefix('{id}/tags')->group(function () use ($prefix) {
                Route::post('', 'attach')->name("admin.{$prefix}.persons.tags.attach");

                Route::delete('', 'detach')->name("admin.{$prefix}.persons.tags.detach");
            });

            /**
             * Activity routes.
             */
            Route::controller(PersonActivityController::class)->prefix('{id}/activities')->group(function () use ($prefix) {
                Route::get('', 'index')->name("admin.{$prefix}.persons.activities.index");
            });
        });

        /**
         * Organization routes.
         */
        Route::controller(OrganizationController::class)->prefix('organizations')->group(function () use ($prefix) {
            Route::get('', 'index')->name("admin.{$prefix}.organizations.index");

            Route::get('create', 'create')->name("admin.{$prefix}.organizations.create");

            Route::post('create', 'store')->name("admin.{$prefix}.organizations.store");

            Route::get('fetch/{id}', 'fetch')->name("admin.{$prefix}.organizations.fetch");

            Route::get('search-customers', 'searchCustomers')->name("admin.{$prefix}.organizations.search_customers");

            Route::get('view/{id}', 'show')->name("admin.{$prefix}.organizations.view");

            Route::get('edit/{id?}', 'edit')->name("admin.{$prefix}.organizations.edit");

            Route::put('edit/{id}', 'update')->name("admin.{$prefix}.organizations.update");

            Route::get('{id}', function ($id) use ($prefix) {
                return redirect()->route("admin.{$prefix}.organizations.view", $id);
            })->name("admin.{$prefix}.organizations.show")->where('id', '[0-9]+');

            Route::delete('{id}', 'destroy')->name("admin.{$prefix}.organizations.delete");

            Route::put('mass-destroy', 'massDestroy')->name("admin.{$prefix}.organizations.mass_delete");

            /**
             * Activity routes.
             */
            Route::controller(ActivityController::class)->prefix('{id}/activities')->group(function () use ($prefix) {
                Route::get('', 'index')->name("admin.{$prefix}.organizations.activities.index");
            });
        });

        /**
         * Organization files routes.
         */
        Route::controller(OrganizationFileController::class)->prefix('organizations')->group(function () use ($prefix) {
            Route::post('{id}/files', 'store')->name("admin.{$prefix}.organizations.files.store");
            Route::delete('files/{id}', 'destroy')->name("admin.{$prefix}.organizations.files.delete");
        });
    });
};

// Register routes for contacts (legacy support)
$registerContactRoutes('contacts');

// Register routes for customers
$registerContactRoutes('customers');

// Register routes for vendors
$registerContactRoutes('vendors');

// Register routes for employees
Route::prefix('employees')->group(function () {
    Route::controller(PersonController::class)->prefix('persons')->group(function () {
        Route::get('', 'index')->name('admin.employees.persons.index');

        Route::get('create', 'create')->name('admin.employees.persons.create');

        Route::post('create', 'store')->name('admin.employees.persons.store');

        Route::get('view/{id}', 'show')->name('admin.employees.persons.view');

        Route::get('edit/{id}', 'edit')->name('admin.employees.persons.edit');

        Route::put('edit/{id}', 'update')->name('admin.employees.persons.update');

        Route::get('search', 'search')->name('admin.employees.persons.search');

        Route::middleware(['throttle:100,60'])->delete('{id}', 'destroy')->name('admin.employees.persons.delete');

        Route::post('mass-destroy', 'massDestroy')->name('admin.employees.persons.mass_delete');

        Route::controller(TagController::class)->prefix('{id}/tags')->group(function () {
            Route::post('', 'attach')->name('admin.employees.persons.tags.attach');

            Route::delete('', 'detach')->name('admin.employees.persons.tags.detach');
        });

        Route::controller(PersonActivityController::class)->prefix('{id}/activities')->group(function () {
            Route::get('', 'index')->name('admin.employees.persons.activities.index');
        });
    });
});
