<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;
use Itstudioat\Spa\Http\Controllers\Homepage\HomepageController;


// Globales Throttle
Route::middleware(['throttle:global'])->group(function () {

    // Alles wird gethrottelt
    Route::middleware(['throttle:api'])->group(function () {

        /* HOMEPAGE ROUTES */
        Route::name('spa.homepage.')->group(function () {
            Route::get('/homepage/config',  [HomepageController::class, 'config']);
        });

        /* ADMIN ROUTES */

        Route::middleware(['web'])->name('spa.admin.')->group(function () {
            Route::get('/admin/config',  [AdminController::class, 'config']);
            Route::post('/admin/login_step_1',  [AdminController::class, 'loginStep1']);
            Route::post('/admin/login_step_2',  [AdminController::class, 'loginStep2']);
            Route::post('/admin/login_step_3',  [AdminController::class, 'loginStep3']);

            Route::post('/admin/password_unknown_step_1',  [AdminController::class, 'passwordUnknownStep1']);
            Route::post('/admin/password_unknown_step_2',  [AdminController::class, 'passwordUnknownStep2']);
            Route::post('/admin/password_unknown_step_3',  [AdminController::class, 'passwordUnknownStep3']);
        });
    });
});
