<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Spa\RouteController;
use Itstudioat\Spa\Http\Controllers\Admin\RoleController;
use Itstudioat\Spa\Http\Controllers\Admin\UserController;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;
use Itstudioat\Spa\Http\Controllers\Admin\NavigationController;
use Itstudioat\Spa\Http\Controllers\Admin\UserWithRoleController;
use Itstudioat\Spa\Http\Controllers\Homepage\HomepageController;

// Globales Throttle
Route::middleware(['throttle:global', 'throttle:api'])->group(function () {


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

    /* vom User ausgelöste APis zur E-Mail-Verifikation */
    Route::post('/admin/users/send_verification_email_initialized_from_user',  [UserController::class, 'sendVerificationEmailInitializedFromUser']);
    Route::post('/admin/users/email_verification',  [UserController::class, 'emailVerification']);

    /* SANCTUM */
    Route::middleware(['auth:sanctum', 'api-allowed:admin'])->group(function () {
        Route::post('/admin/execute_logout',  [AdminController::class, 'executeLogout']);

        // navigation, menus
        Route::get('/admin/navigation/profile_menu',  [NavigationController::class, 'profileMenu']);
        Route::get('/admin/navigation/user_menu',  [NavigationController::class, 'userMenu']);

        // users
        Route::apiResource('/admin/users', UserController::class);
        Route::put('/admin/users/update_profile/{user}',  [UserController::class, 'updateProfile']);
        Route::post('/admin/users/destroy_multiple',  [UserController::class, 'destroyMultiple']);
        Route::post('/admin/users/update_with_code',  [UserController::class, 'updateWithCode']);
        Route::post('/admin/users/save_password',  [UserController::class, 'savePassword']);
        Route::post('/admin/users/save_password_with_code',  [UserController::class, 'savePasswordWithCode']);
        Route::post('/admin/users/send_verification_email',  [UserController::class, 'sendVerificationEmail']);



        // roles
        Route::apiResource('/admin/roles', RoleController::class);
        Route::post('/admin/roles/destroy_multiple',  [RoleController::class, 'destroyMultiple']);

        // users_with_roles
        Route::get('/admin/users_with_roles/roles',  [UserWithRoleController::class, 'roles']);
        Route::post('/admin/users_with_roles/roles',  [UserWithRoleController::class, 'saveUserRoles']);
        Route::apiResource('/admin/users_with_roles', UserWithRoleController::class);
        Route::apiResource('/admin/users_with_roles', UserWithRoleController::class);
    });
});
