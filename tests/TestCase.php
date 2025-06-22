<?php

namespace Tests;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Middleware\ApiAllowed;
use App\Http\Middleware\WebAllowed;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Itstudioat\Spa\SpaServiceProvider;

use Illuminate\Cache\RateLimiting\Limit;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\SanctumServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

use Spatie\Permission\PermissionServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected User $user;

    /**
     * Setup environment and run before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();


        config(['app.key' => 'base64:' . base64_encode(random_bytes(32))]);

        /*
        // Lade Migrationen, z.B. nur die von Spatie (deine eigenen auskommentiert)
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/spatie/laravel-permission/database/migrations');

        dump(scandir(__DIR__ . '/../database/migrations'));
        dump(scandir(__DIR__ . '/../vendor/spatie/laravel-permission/database/migrations'));

        $this->artisan('migrate')->run();
*/

        /*
        $this->loadMigrationsFrom([
            __DIR__ . '/../database/migrations',
            __DIR__ . '/../vendor/spatie/laravel-permission/database/migrations',
        ]);
*/
        // Factory-Namenskonventionen für deine Modelle
        Factory::guessFactoryNamesUsing(fn(string $modelName) => 'Database\\Factories\\Spa' . class_basename($modelName) . 'Factory');


        // Beispieluser erstellen für Tests
        $this->user = User::factory()->create([
            'email'        => 'kron@naturwelt.at',
            'password'     => Hash::make('password123'),
            'is_active'    => true,
            'confirmed_at' => now(),
        ]);

        // Optional: artisan spa:update Befehl ausführen
        $this->artisan('spa:update')->run();
    }

    /**
     * Registriere Package Service Provider.
     */
    protected function getPackageProviders($app): array
    {
        return [
            SpaServiceProvider::class,
            PermissionServiceProvider::class,
        ];
    }

    /**
     * Beispiel Middleware Registrierung im Test-Environment.
     */
    protected function defineEnvironment($app): void
    {
        $app['router']->aliasMiddleware('web-allowed', WebAllowed::class);
        $app['router']->aliasMiddleware('api-allowed', ApiAllowed::class);
        $app['router']->aliasMiddleware('auth:sanctum', EnsureFrontendRequestsAreStateful::class);
    }

    /**
     * Setup zusätzliche Environment-Konfiguration.
     */
    public function getEnvironmentSetUp($app): void
    {



        // Wichtig: Datenbanktabellen löschen (Vorsicht! Nur in Testumgebung)
        Schema::dropAllTables();

        // Manuelle Migrationen ausführen (evtl. Migrationen als Klassen importieren und aufrufen)
        $migrations = [
            __DIR__ . '/../database/migrations/0001_01_01_000000_create_users_table.php',
            __DIR__ . '/../database/migrations/00001_update_users_table.php',
            __DIR__ . '/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub',
        ];

        foreach ($migrations as $migrationFile) {
            $migration = include $migrationFile;
            $migration->up();
        }


        // Laravel Sanctum Service Provider registrieren
        $app->register(SanctumServiceProvider::class);

        // Sanctum Auth Guard konfigurieren
        Config::set('auth.guards.sanctum', [
            'driver'   => 'sanctum',
            'provider' => 'users',
        ]);

        // Spatie Permission Config anpassen
        $app['config']->set('permission.models.permission', Permission::class);
        $app['config']->set('permission.models.role', Role::class);
        $app['config']->set('permission.cache.key', 'spatie.permission.cache');
        $app['config']->set('permission.column_names.role_pivot_key', 'role_id');
        $app['config']->set('permission.column_names.permission_pivot_key', 'permission_id');

        // Rate Limiter konfigurieren
        RateLimiter::for('api', fn(Request $request) => Limit::perMinute(config('spa.api_throttle', 60))->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('web', fn(Request $request) => Limit::perMinute(config('spa.web_throttle', 60))->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('global', fn(Request $request) => Limit::perMinute(config('spa.global_throttle', 1000)));
    }
}
