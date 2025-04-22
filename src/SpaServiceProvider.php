<?php

namespace Itstudioat\Spa;


use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\CreateUser;
use Itstudioat\Spa\Commands\InstallMe;
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
            ->name('spa');
    }

    public function registeringPackage()
    {
        $this->publishes([
            __DIR__ . '/../config/spa.php' => config_path('spa.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources' => resource_path(),
        ], 'resources');

        $this->publishes([
            __DIR__ . '/../routes' => base_path('/routes'),
        ], 'routes');

        $this->publishes([
            __DIR__ . '/../src/Commands' => app_path('/Commands'),
            __DIR__ . '/../src/Http' => app_path('/Http'),
            __DIR__ . '/../src/Models' => app_path('/Models'),
            __DIR__ . '/../src/Notifications' => app_path('/Notifications'),
            __DIR__ . '/../src/Services' => app_path('/Services'),
            __DIR__ . '/../src/Traits' => app_path('/Traits'),
        ], 'aps-app');

        $this->publishes([
            __DIR__ . '/../stubs/vite.config.js' => base_path(),
        ], 'spa-stubs');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'spa-migrations');

        $this->publishes([
            __DIR__ . '/../config/spa.php' => config_path('spa.php'),
            __DIR__ . '/../resources' => resource_path(),
            __DIR__ . '/../routes' => base_path('/routes'),
            __DIR__ . '/../src/Commands' => app_path('/commands'),
            __DIR__ . '/../src/Commands' => app_path('/Commands'),
            __DIR__ . '/../src/Http' => app_path('/Http'),
            __DIR__ . '/../src/Models' => app_path('/Models'),
            __DIR__ . '/../src/Notifications' => app_path('/Notifications'),
            __DIR__ . '/../src/Services' => app_path('/Services'),
            __DIR__ . '/../src/Traits' => app_path('/Traits'),
            __DIR__ . '/../stubs/vite.config.js' => base_path(),
        ], 'spa-all');
    }

    public function packageRegistered() {}

    public function bootingPackage()
    {
        $this->loadRoutes();
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
