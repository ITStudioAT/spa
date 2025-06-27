<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SpaPackagesTest extends TestCase
{
    public function test_spa_packages_command_outputs_expected_lines()
    {
        // Führe den spa:packages-Command aus (real, ohne Mocking)
        $this->artisan('spa:packages')
            ->expectsOutputToContain('Installing fixed @vitejs/plugin-vue')
            ->expectsOutputToContain('Installing latest pinia')
            ->expectsOutputToContain('Installing latest vue-router')
            ->expectsOutputToContain('Installing fixed dev version vite@^6.0.0')
            ->expectsOutputToContain('✅ Alle Pakete installiert.')
            ->assertExitCode(0);
    }
}
