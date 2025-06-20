<?php

namespace Tests\Commands;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

class SpaPackagesTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Itstudioat\Spa\SpaServiceProvider::class,
        ];
    }

    public function test_spa_packages_updates_package_json()
    {
        $packageJsonPath = base_path('package.json');

        // Backup falls package.json schon existiert
        $backupExists = File::exists($packageJsonPath);
        $backupPath = base_path('package.json.backup');

        if ($backupExists) {
            File::copy($packageJsonPath, $backupPath);
        }

        // Erstelle eine einfache package.json zum Testen
        $initialJson = json_encode([
            "dependencies" => [
                "some-package" => "^1.0"
            ],
            "devDependencies" => []
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        File::put($packageJsonPath, $initialJson);

        // Kommando ausführen
        $this->artisan('spa:packages')
            ->expectsOutput('✅ Vue dependencies added to package.json. Run npm install to install them.')
            ->assertExitCode(0);

        // Dateiinhalt prüfen
        $updatedJson = json_decode(File::get($packageJsonPath), true);

        $this->assertArrayHasKey('@vitejs/plugin-vue', $updatedJson['dependencies']);
        $this->assertArrayHasKey('pinia', $updatedJson['dependencies']);
        $this->assertArrayHasKey('axios', $updatedJson['devDependencies']);
        $this->assertArrayHasKey('vite', $updatedJson['devDependencies']);

        // Aufräumen: Test-package.json löschen
        File::delete($packageJsonPath);

        // Backup wiederherstellen falls vorhanden
        if ($backupExists) {
            File::move($backupPath, $packageJsonPath);
        }
    }
}
