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

        $this->loadRoutes();

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
