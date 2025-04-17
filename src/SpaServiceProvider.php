<?php

namespace Itstudioat\Spa;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Itstudioat\Spa\Commands\SpaCommand;

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
            ->hasViews()
            ->hasMigration('create_spa_table')
            ->hasCommand(SpaCommand::class);
    }
}
