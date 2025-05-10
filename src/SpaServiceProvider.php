<?php

namespace Itstudioat\Spa;

use App\Models\User;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\CreateUser;
use Itstudioat\Spa\Commands\Fake\UserFake;
use Itstudioat\Spa\Commands\SpaComplete;
use Itstudioat\Spa\Commands\SpaPackages;
use Itstudioat\Spa\Commands\SpaUpdate;
use Itstudioat\Spa\Commands\SyncRoutes;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SpaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('spa')
            ->hasMigration('00001_update_users_table')
            ->hasCommands([
                CreateUser::class,
                SpaPackages::class,
                SpaComplete::class,
                SpaUpdate::class,
                SyncRoutes::class,
                UserFake::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hello, and welcome to my great new package!');
                    })
                    ->publishMigrations()
                    ->publishConfigFile()
                    ->endWith(function (InstallCommand $command) {
                        $command->info('Have a great day!');
                    });
            });
    }

    public function packageRegistered()
    {

        // One Time Publishing
        $this->publishes([
            __DIR__ . '/../stubs/vite.config.js' => base_path('vite.config.js'),
            __DIR__ . '/../stubs/images' => storage_path('/app/public/images'),

            __DIR__ . '/../bootstrap/app.php' => base_path('/bootstrap/app.php'),
            __DIR__ . '/../config' => config_path(),
            __DIR__ . '/../database/factories' => base_path('/database/factories'),
            __DIR__ . '/../resources' => resource_path(),
            __DIR__ . '/../routes' => base_path('/routes'),
            __DIR__ . '/../src/Enums' => app_path('/Enums'),
            __DIR__ . '/../src/Http' => app_path('/Http'),
            __DIR__ . '/../src/Models' => app_path('/Models'),
            __DIR__ . '/../src/Providers' => app_path('/Providers'),
            __DIR__ . '/../src/Services' => app_path('/Services'),

        ], 'spa-all');
    }

    public function bootingPackage()
    {
        $this->app['router']->pushMiddlewareToGroup('web', StartSession::class);

        // Routen werden beim Booten des Pakets geladen
        Route::model('user', User::class);
        $this->loadRoutes();
    }

    protected function loadRoutes()
    {

        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        // Lade API-Routen ohne Middleware
        if (file_exists(__DIR__ . '/../routes/api.php')) {
            Route::prefix('api') // API-Routen mit 'api' Prefix
                ->group(__DIR__ . '/../routes/api.php');
        }
    }
}
