<?php

namespace App\Tests;

use App\Models\User;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\SpaServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\SanctumServiceProvider;
use App\Http\Middleware\ApiAllowed;
use App\Http\Middleware\WebAllowed;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class TestCase extends Orchestra
{
    /**
     * Set up the environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();



        //$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/spatie/laravel-permission/database/migrations');


        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\\Factories\\Spa' . class_basename($modelName) . 'Factory';
        });

        $this->user = User::factory()->create([
            'email' => 'kron@naturwelt.at',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'confirmed_at' => now(),
        ]);

        $this->artisan('spa:update')->run();
    }

    /**
     * Define the package providers for the test environment.
     */
    protected function getPackageProviders($app)
    {
        return [
            SpaServiceProvider::class, // Register the package's service providerc
            PermissionServiceProvider::class,
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
        $migration = include __DIR__ . '/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub';
        $migration->up();


        $app->register(SanctumServiceProvider::class);

        // Register the sanctum guard in the test environment
        Config::set('auth.guards.sanctum', [
            'driver' => 'sanctum',
            'provider' => 'users',
        ]);

        $app['config']->set('permission.models.permission', \Spatie\Permission\Models\Permission::class);
        $app['config']->set('permission.models.role', \Spatie\Permission\Models\Role::class);
        $app['config']->set('permission.cache.key', 'spatie.permission.cache');
        $app['config']->set('permission.column_names.role_pivot_key', 'role_id');
        $app['config']->set('permission.column_names.permission_pivot_key', 'permission_id');


        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('spa.api_throttle', 60))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(config('spa.web_throttle', 60))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(config('spa.global_throttle', 1000));
        });



        $app['router']->aliasMiddleware('web-allowed', WebAllowed::class);
        $app['router']->aliasMiddleware('api-allowed', ApiAllowed::class);
        $app['router']->aliasMiddleware('auth:sanctum', EnsureFrontendRequestsAreStateful::class);
    }
}
