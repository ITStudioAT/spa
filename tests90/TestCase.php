<?php

namespace Itstudioat\Spa\Tests;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Itstudioat\Spa\Http\Middleware\WebAllowed;
use Itstudioat\Spa\SpaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Set up the environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // $this->loadLaravelMigrations();
        // $this->artisan('migrate');
        /*
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });
        */
    }

    /**
     * Define the package providers for the test environment.
     */
    protected function getPackageProviders($app)
    {
        return [
            SpaServiceProvider::class, // Register the package's service provider
        ];
    }

    protected function defineEnvironment($app)
    {
        // Example: Register a middleware in the testing environment
        $app['router']->aliasMiddleware('web-allowed', WebAllowed::class);
    }

    /**
     * Set up the environment configuration for the test.
     */
    public function getEnvironmentSetUp($app)
    {

        Schema::DropAllTables();
        $migration = include __DIR__ . '/../database/migrations/0001_01_01_000000_create_users_table.php';
        $migration->up();

        $migration = include __DIR__ . '/../database/migrations/00001_update_users_table.php';
        $migration->up();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('spa.api_throttle', 60))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(config('spa.web_throttle', 60))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(config('spa.global_throttle', 1000));
        });
    }
}
