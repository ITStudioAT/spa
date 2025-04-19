<?php

use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;

// Globales Throttle
Route::middleware(['throttle:global'])->group(function () {

    /* ADMIN ROUTES */
    Route::middleware(['throttle:web'])->group(function () {
        Route::get('/admin/login', function () {
            return view('spa::admin');
        })->name('login');

        Route::get('/admin/unknown_password', function () {
            return view('spa::admin');
        });
    });


    // Alles wird gethrottlet
    Route::name('spa')        // All routes inside this group will be named with /spa
        ->middleware(['throttle:web'])->group(function () {


            /* HOMEPAGE ROUTES */
            Route::get('/', function () {
                return view('spa::homepage');
            });

            Route::get('/homepage/{any?}', function () {
                return view('spa::homepage');
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
