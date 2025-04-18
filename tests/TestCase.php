<?php

namespace Itstudioat\Spa\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Itstudioat\Spa\SpaServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Manuell den APP_KEY setzen
        config(['app.key' => 'base64:' . base64_encode(random_bytes(32))]);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('spa.api_throttle', 60))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(config('spa.web_throttle', 60))->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(config('spa.global_throttle', 60));
        });
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
