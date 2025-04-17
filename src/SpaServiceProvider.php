<?php

namespace Itstudioat\Spa;


use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\CreateUser;
use Itstudioat\Spa\Commands\SpaInstall;
use Spatie\LaravelPackageTools\Package;
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
            ->hasCommand(SpaInstall::class)
            ->hasCommand(CreateUser::class)
        ;
    }

    public function bootingPackage()
    {

        $this->loadRoutes();

        // web-routes
        $this->publishes([
            __DIR__ . '/../routes/web.php' => base_path('routes/'),
        ], 'spa-routes');

        // resources
        $this->publishes([
            __DIR__ . '/../resources/dist' => base_path('resources/'),
        ], 'spa-assets');

        // vite.config.js
        $this->publishes([
            __DIR__ . '/../stubs/vite.config.js' => base_path('vite.config.js'),
        ], 'spa-vite-config');

        // Traits
        $this->publishes([
            __DIR__ . '/../Traits' => app_path('Traits'),
        ], 'spa-traits');

        // You can also add more publishes here (config, vite config, etc.)
    }

    protected function loadRoutes()
    {
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
        if (file_exists(__DIR__ . '/../routes/api.php')) {
            Route::prefix('api')
                ->group(__DIR__ . '/../routes/api.php');
        }
    }
}
