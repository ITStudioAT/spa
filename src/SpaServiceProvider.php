<?php

namespace Itstudioat\Spa;



use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\InstallMe;
use Itstudioat\Spa\Commands\UpdateSpa;
use Itstudioat\Spa\Commands\CreateUser;
use Itstudioat\Spa\Commands\SyncRoutes;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\Commands\Concerns;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class SpaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('spa')
            // Falls du spezielle Installationsbefehle ausführen möchtest, kannst du hier 'hasInstallCommand()' hinzufügen
            ->hasConfigFile()
            ->hasViews()
            ->hasRoutes(['web', 'api'])
            ->hasCommands([
                CreateUser::class,
                InstallMe::class,  // Dein benutzerdefinierter Installationsbefehl
                UpdateSpa::class,
                SyncRoutes::class,
            ]);
    }

    public function packageRegistered()
    {
        // Hier werden die Ressourcen veröffentlicht

        // Konfigurationsdateien

        $this->publishes([
            __DIR__ . '/../config/spa.php' => config_path('spa.php'),
        ], 'spa-config');

        // Ressourcen (Bilder, Views, etc.)
        $this->publishes([
            __DIR__ . '/../resources' => resource_path(),
        ], 'spa-resources');

        // Routen und bootstrap/app.php
        $this->publishes([
            __DIR__ . '/../bootstrap/app.php' => base_path('/bootstrap/app.php'),
            __DIR__ . '/../routes/meta' => base_path('/routes/meta'),
        ], 'spa-root');


        // App-Ressourcen (Commands, Http, Models, Notifications, Services, Traits)
        $this->publishes([
            __DIR__ . '/../src/Models' => app_path('/Models'),
            __DIR__ . '/../src/Providers' => app_path('/Providers'),
            /*
            __DIR__ . '/../src/Commands' => app_path('/Commands'),
            __DIR__ . '/../src/Http' => app_path('/Http'),
            __DIR__ . '/../src/Notifications' => app_path('/Notifications'),
            __DIR__ . '/../src/Services' => app_path('/Services'),
            __DIR__ . '/../src/Traits' => app_path('/Traits'),
            */

        ], 'spa-app');


        // Stubs (z.B. Vite-Konfiguration)
        $this->publishes([
            __DIR__ . '/../stubs/vite.config.js' => base_path('vite.config.js'),
        ], 'spa-vite');

        // Migrationen
        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'spa-migrations');


        $this->publishes([
            __DIR__ . '/../config/spa.php' => config_path('spa.php'),
            __DIR__ . '/../resources' => resource_path(),
            __DIR__ . '/../bootstrap/app.php' => base_path('/bootstrap/app.php'),
            __DIR__ . '/../stubs/vite.config.js' => base_path('vite.config.js'),
            __DIR__ . '/../src/Models' => app_path('/Models'),
            __DIR__ . '/../src/Providers' => app_path('/Providers'),
        ], 'spa-all');
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
