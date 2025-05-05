<?php

namespace Itstudioat\Spa\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Itstudioat\Spa\SpaServiceProvider;


class TestCase extends Orchestra
{
    /**
     * Set up the environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
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

    /**
     * Set up the environment configuration for the test.
     */
    public function getEnvironmentSetUp($app)
    {
        // Set up the database configuration for testing
        config()->set('database.default', 'testing');
    }
}
