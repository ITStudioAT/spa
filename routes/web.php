<?php

use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;

// Globales Throttle
Route::middleware(['throttle:global'])->group(function () {

    // Alles wird gethrottlet
    Route::middleware(['throttle:web'])->group(function () {

        /* HOMEPAGE ROUTES */
        Route::get('/', function () {
            return view('spa::homepage');
        });

        Route::get('/homepage/{any?}', function () {
            return view('spa::homepage');
        });

        /* ADMIN ROUTES */
        Route::get('/admin/{any?}', function () {
            return view('spa::admin');
        })->where('any', '.*');

        /* APPLICATION ROUTES */
        Route::get('/application/{any?}', function () {
            return view('spa::application');
        })->where('any', '.*');
    });
});
