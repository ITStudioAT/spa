<?php

namespace Itstudioat\Spa;


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
            ->hasMigration('create_my_models_table')
            ->hasCommand(SpaInstall::class);
    }

    public function bootingPackage()
    {
        $this->publishes([
            __DIR__ . '/../stubs/vite.config.js' => base_path('vite.config.js'),
        ], 'spa-vite-config');
    }
}
