<?php

namespace Itstudioat\Spa;


use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\SpaInstall;
use Spatie\LaravelPackageTools\Package;
use Itstudioat\Spa\Http\Controllers\AdminController;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SpaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('spa')
            ->hasConfigFile()
            ->hasMigration('update_users_table')
            ->hasViews()
            ->hasCommand(SpaInstall::class);
    }

    public function bootingPackage()
    {

        Route::macro('spa', function ($baseUrl = 'spa') {
            Route::prefix($baseUrl)->group(function () {
                /* Route::get('/', [AdminController::class, 'index']); */
                Route::get('/', function () {
                    return view('spa::homepage');
                });

                Route::get('/homepage/{any?}', function () {
                    return view('homepage');
                })->where('any', '.*');

                Route::get('/admin/{any?}', function () {
                    return view('admin');
                })->where('any', '.*');

                Route::get('/application/{any?}', function () {
                    return view('application');
                })->where('any', '.*');
            });
        });

        $this->publishes([
            __DIR__ . '/../resources/dist' => base_path('resources/'),
        ], 'spa-assets');

        $this->publishes([
            __DIR__ . '/../stubs/vite.config.js' => base_path('vite.config.js'),
        ], 'spa-vite-config');

        $this->publishes([
            __DIR__ . '/../Traits' => app_path('Traits'),
        ], 'spa-traits');

        // You can also add more publishes here (config, vite config, etc.)
    }
}
