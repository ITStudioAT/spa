<?php

use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;




Route::middleware(['throttle:global', 'throttle:web'])->group(function () {


    Route::get('/admin/login', function () {
        return view('spa::admin');
    })->name('login');

    // Alles wird gethrottlet
    Route::name('spa')->group(function () {

        /* HOMEPAGE ROUTES */
        Route::get('/', function () {
            return view('spa::homepage');
        });

        Route::get('/homepage/{any?}', function () {
            return view('spa::homepage');
        });

        /* ADMIN ROUTES */


        Route::get('/admin/unknown_password', function () {
            return view('spa::admin');
        });


        Route::get('/admin/register', function () {
            abort_unless(config('spa.register_admin_allowed'), 403);
            return view('spa::admin');
        });



        Route::get('/admin/{any?}', function () {
            return view('spa::admin');
        })->where('any', '.*')->middleware(['auth:sanctum']);

        /* APPLICATION ROUTES */
        Route::get('/application/{any?}', function () {
            return view('spa::application');
        })->where('any', '.*');
    });
});
