<?php

namespace Itstudioat\Spa;



use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\CreateUser;
use Itstudioat\Spa\Commands\SpaComplete;
use Itstudioat\Spa\Commands\SpaPackages;
use Itstudioat\Spa\Commands\SpaUpdate;
use Itstudioat\Spa\Commands\SyncRoutes;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class SpaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('spa')
            ->hasConfigFile()
            ->hasViews()
            ->discoversMigrations()
            ->hasRoutes(['web', 'api'])
            ->hasCommands([
                CreateUser::class,
                SpaPackages::class,
                SpaComplete::class,
                SpaUpdate::class,
                SyncRoutes::class,
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
            __DIR__ . '/../bootstrap/app.php' => base_path('/bootstrap/app.php'),
            __DIR__ . '/../routes/meta' => base_path('/routes/meta'),
            __DIR__ . '/../src/Models' => app_path('/Models'),
            __DIR__ . '/../src/Providers' => app_path('/Providers'),
        ], 'spa-once');

        // Multi Time Publishing with
        $this->publishes([
            __DIR__ . '/../resources' => resource_path(),
        ], 'spa-multi');
    }

    public function bootingPackage()
    {
        // Routen werden beim Booten des Pakets geladen
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
