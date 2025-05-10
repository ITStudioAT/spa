<?php

use Composer\InstalledVersions;

it('can test /api/admin/config', function () {

    $this->getJson('/api/admin/config')
        ->assertOk()
        ->assertJson([
            'logo' => config('spa.logo', ''),
            'copyright' => config('spa.copyright', ''),
            'title' => config('spa.title', 'Fresh Laravel'),
            'company' => config('spa.company', 'ItStudio.at'),
            'version' => InstalledVersions::getPrettyVersion('itstudioat/spa'),
            'register_admin_allowed' => config('spa.register_admin_allowed', false),
            'timeout' => config('spa.timeout', 3000),
        ]);
});
