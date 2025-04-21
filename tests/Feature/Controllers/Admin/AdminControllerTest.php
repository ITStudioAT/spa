<?php

use Composer\InstalledVersions;
use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;

it('calls config: /api/admin/config and retuns as json', function () {
    config([
        'spa.logo' => 'logo.svg',
        'spa.copyright' => '© 2025',
        'spa.timeout' => 1234,
        'spa.title' => 'Test App',
        'spa.company' => 'TestCompany',
        'spa.register_admin_allowed' => true,
    ]);

    // Optional: Falls du InstalledVersions mocken möchtest
    InstalledVersions::reload([
        [
            'name' => 'itstudioat/spa',
            'pretty_version' => '1.2.3',
            'version' => '1.2.3.0',
        ],
    ]);

    // Wenn du nicht mockst, kannst du auch die echte Version holen
    $version = InstalledVersions::getPrettyVersion('itstudioat/spa');

    $response = getJson('/api/admin/config'); // deine Route

    $response->assertStatus(200);
    $response->assertExactJson([
        'logo' => 'logo.svg',
        'copyright' => '© 2025',
        'timeout' => 1234,
        'title' => 'Test App',
        'company' => 'TestCompany',
        'version' => $version,
        'register_admin_allowed' => true,
    ]);
});
