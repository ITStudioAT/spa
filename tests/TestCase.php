<?php

namespace Itstudioat\Spa\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Itstudioat\Spa\SpaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Führe Laravel-Migrationen aus (users, etc.)
        $this->loadLaravelMigrations();

        // Führe ggf. packageeigene Migrationen aus
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Itstudioat\\Spa\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        Route::spa();
    }

    protected function getPackageProviders($app)
    {
        return [
            SpaServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        /*
        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
            */
    }
}
