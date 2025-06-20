<?php

namespace Tests\Feature;

use Itstudioat\Spa\Commands\SpaComplete;
use Illuminate\Support\Facades\Process;
use Orchestra\Testbench\TestCase;
use Mockery;

class SpaCompleteCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Itstudioat\Spa\SpaServiceProvider::class,
        ];
    }

    public function test_spa_complete_runs_all_steps()
    {
        // Partial Mock des Commands, damit Konstruktor ausgeführt wird
        $command = $this->partialMock(\Itstudioat\Spa\Commands\SpaComplete::class, function ($mock) {
            // stubbe call() so dass alle Sub-Commands "erfolgreich" simuliert werden
            $mock->shouldReceive('call')->andReturn(0);
        });

        // Process Facade mocken
        \Illuminate\Support\Facades\Process::shouldReceive('timeout')
            ->with(300)
            ->andReturnSelf();

        \Illuminate\Support\Facades\Process::shouldReceive('run')
            ->andReturnUsing(function ($command, $callback) {
                $callback('info', "npm install output\n");
                return true;
            });

        // Artisan Befehl ausführen und Ausgaben testen
        $this->artisan('spa:complete')
            ->expectsOutput('✅ Migrations executed.')
            ->expectsOutput('✅ All Laravel-Spa-Files are published.')
            ->expectsOutput('✅ Spatie Permissions published and migrated.')
            ->expectsOutput('✅ Laravel Sanctum installed.')
            ->expectsOutput('Running npm install...')
            ->expectsOutput('✅ npm install finished.')
            ->expectsOutput('✅ User created')
            ->expectsOutput('✅ Update finished')
            ->expectsOutput('✅ Storage linked')
            ->expectsOutput("⚠️  Dont forget to set 'supports_credentials' => true in config/cors.php")
            ->expectsOutput('✅ Installation complete!')
            ->assertExitCode(0);
    }
}
