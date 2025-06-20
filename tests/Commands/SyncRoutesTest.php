<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Itstudioat\Spa\Commands\RoutesSync;
use Orchestra\Testbench\TestCase;

class SyncRoutesCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Itstudioat\Spa\SpaServiceProvider::class,
        ];
    }

    public function test_routes_sync_creates_meta_files_and_folder()
    {
        $metaPath = base_path('routes/meta/web');
        $homepageFile = $metaPath . '/homepage.php';
        $testFile = $metaPath . '/test.php';

        if (File::exists($metaPath)) {
            File::deleteDirectory($metaPath);
        }

        $fakeRoutes = collect([
            new class {
                public function uri()
                {
                    return '/';
                }
            },
            new class {
                public function uri()
                {
                    return 'test/{id}';
                }
            },
            new class {
                public function uri()
                {
                    return 'api/users';
                }
            }
        ]);

        Route::shouldReceive('getRoutes')->once()->andReturn($fakeRoutes);

        $this->artisan(RoutesSync::class)
            ->expectsOutput('🚀 Starting route sync...')
            ->expectsOutput("📁 Created folder: $metaPath")
            ->expectsOutput("✅ Created metadata file: $homepageFile")
            ->expectsOutput("✅ Created metadata file: $testFile")
            ->expectsOutput('🎉 Route sync completed.')
            ->assertExitCode(0);

        $this->assertTrue(File::exists($metaPath), "Meta folder was not created.");
        $this->assertTrue(File::exists($homepageFile), "Homepage meta file was not created.");
        $this->assertTrue(File::exists($testFile), "Test meta file was not created.");

        $homepageContent = File::get($homepageFile);
        $testContent = File::get($testFile);

        $this->assertStringContainsString("'/'", $homepageContent);
        $this->assertStringContainsString('[]', $homepageContent);

        $this->assertStringContainsString('test/*', $testContent);
        $this->assertStringContainsString('[]', $testContent);
    }
}
