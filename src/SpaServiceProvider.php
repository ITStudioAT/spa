<?php

namespace Itstudioat\Spa;


use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\CreateUser;
use Itstudioat\Spa\Commands\SpaInstall;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

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
            ->hasAssets()
            ->hasCommand(SpaInstall::class)
            ->hasCommand(CreateUser::class)
            ->hasConfigFile()
            ->hasMigration('00001_update_users_table')
            ->runsMigrations()
            ->hasRoutes(['web', 'admin'])
            ->hasViews()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hello, and welcome to my great new package!');
                    })
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        $command->info('Have a great day!');
                    });
            });
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
