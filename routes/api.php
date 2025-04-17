<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\AdminController;
use App\Http\Controllers\Homepage\HomepageController;

// Globales Throttle
Route::middleware(['throttle:global'])->group(function () {

    // Alles wird gethrottelt
    Route::middleware(['throttle:api'])->group(function () {

        /* HOMEPAGE ROUTES */
        Route::name('spa.homepage.')->group(function () {
            Route::get('/homepage/config',  [HomepageController::class, 'config']);
        });

        /* ADMIN ROUTES */

        Route::name('spa.admin.')->group(function () {
            Route::get('/admin/config',  [AdminController::class, 'config']);
            Route::post('/admin/login_step_1',  [AdminController::class, 'loginStep1']);
            Route::post('/admin/login_step_2',  [AdminController::class, 'loginStep2']);
            Route::post('/admin/login_step_3',  [AdminController::class, 'loginStep3']);
        });
    });
});
