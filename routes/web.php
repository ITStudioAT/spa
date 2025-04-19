<?php

use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Http\Controllers\Admin\AdminController;


Route::get('/auth', function () {
    return "AUTH: " . (auth()->check() ? 'Logged in' : 'Not logged in') . " SESSION: " . print_r(session()->all(), true);
});

Route::get('/sess-test', function () {
    session()->put('foo', 'bar');
    return 'foo: ' . session('foo');
});

// Set the session
Route::get('/sess-set', function () {
    session()->put('test', 'xbarx');
    return "SESSION set.";
});

// Read the session
Route::get('/sess-get', function () {
    return "SESSION: " . session('test');
});




Route::get('/admin/login', function () {
    return view('spa::admin');
})->name('login');

Route::get('/admin/unknown_password', function () {
    return view('spa::admin');
});


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
    Route::get('/admin/{any?}', function () {
        return view('spa::admin');
    })->where('any', '.*')->middleware(['auth:sanctum']);

    /* APPLICATION ROUTES */
    Route::get('/application/{any?}', function () {
        return view('spa::application');
    })->where('any', '.*');
});
