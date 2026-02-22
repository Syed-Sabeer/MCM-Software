<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Website\WebsiteSubmissionsController;

Route::controller(WebsiteSubmissionsController::class)->prefix('website-submissions')->name('admin.website_submissions.')->group(function () {
    Route::get('', 'index')->name('index');

    Route::get('contacts', 'contacts')->name('contacts');
    Route::get('api/contacts', 'getContacts')->name('api.contacts');

    Route::get('careers', 'careers')->name('careers');
    Route::get('api/careers', 'getCareers')->name('api.careers');

    Route::get('contacts/{id}', 'showContact')->name('contact.show');
    Route::get('careers/{id}', 'showCareer')->name('career.show');
});
