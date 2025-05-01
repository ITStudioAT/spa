<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Spa\RouteController;
use Itstudioat\Spa\Http\Controllers\Admin\UserController;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;
use Itstudioat\Spa\Http\Controllers\Homepage\HomepageController;

// Globales Throttle
Route::middleware(['throttle:global', 'throttle:api', 'api-allowed'])->group(function () {




    /***** OTHER ROUTES *****/
    Route::post('/routes/is_route_allowed',  [RouteController::class, 'isRouteAllowed']);

    /***** HOMEPAGE ROUTES *****/

    Route::get('/homepage/config',  [HomepageController::class, 'config']);

    /***** ADMIN ROUTES *****/

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
    Route::middleware(['auth:sanctum'])->group(function () {
        info("roue");
        Route::post('/admin/execute_logout',  [AdminController::class, 'executeLogout']);
        Route::get('/admin/managable_user_roles',  [AdminController::class, 'managableUserRoles']);

        Route::apiResource('/admin/users', UserController::class);
        Route::post('/admin/users/update_with_code',  [UserController::class, 'updateWithCode']);
        Route::post('/admin/users/save_password',  [UserController::class, 'savePassword']);
        Route::post('/admin/users/save_password_with_code',  [UserController::class, 'savePasswordWithCode']);
    });
});
