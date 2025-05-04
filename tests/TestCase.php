<?php

use Itstudioat\Spa\SpaServiceProvider;
use Spatie\Permission\PermissionServiceProvider;



class TestCase extends Orchestra
{

    /**
     * Set up the environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->artisan('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
            '--tag' => 'migrations',
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations'); // if your package has migrations

        $this->artisan('migrate');
    }

    /**
     * Define the package providers for the test environment.
     */
    protected function getPackageProviders($app)
    {
        return [
            SpaServiceProvider::class, // Register the package's service provider
            PermissionServiceProvider::class,
        ];
    }

    /**
     * Set up the environment configuration for the test.
     */
    public function getEnvironmentSetUp($app)
    {
        // Set up the database configuration for testing
        config()->set('database.default', 'testing');
    }
}
