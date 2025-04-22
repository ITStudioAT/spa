<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;
use Itstudioat\Spa\Http\Controllers\Homepage\HomepageController;


// Globales Throttle
Route::middleware(['throttle:global', 'throttle:api'])->group(function () {

    /***** HOMEPAGE ROUTES *****/
    Route::name('spa.homepage.')->group(function () {
        Route::get('/homepage/config',  [HomepageController::class, 'config']);
    });

    /***** ADMIN ROUTES *****/
    Route::name('spa.admin.')->group(function () {
        Route::get('/admin/config',  [AdminController::class, 'config']);

        Route::post('/admin/login_step_1',  [AdminController::class, 'loginStep1']);
        Route::post('/admin/login_step_2',  [AdminController::class, 'loginStep2']);
        Route::post('/admin/login_step_3',  [AdminController::class, 'loginStep3']);

        Route::post('/admin/password_unknown_step_1',  [AdminController::class, 'passwordUnknownStep1']);
        Route::post('/admin/password_unknown_step_2',  [AdminController::class, 'passwordUnknownStep2']);
        Route::post('/admin/password_unknown_step_3',  [AdminController::class, 'passwordUnknownStep3']);
        Route::post('/admin/password_unknown_step_4',  [AdminController::class, 'passwordUnknownStep4']);

        Route::post('/admin/register_step_1',  [AdminController::class, 'registerStep1']);
        Route::post('/admin/register_step_2',  [AdminController::class, 'registerStep2']);
        Route::post('/admin/register_step_3',  [AdminController::class, 'registerStep3']);


        /* SANCTUM */
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/admin/execute_logout',  [AdminController::class, 'executeLogout']);
        });
    });
});
