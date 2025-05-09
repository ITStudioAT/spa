<?php


use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can test /api/homepage/config', function () {

    $response = $this->getJson('/api/homepage/config');
    $response->assertOk();
    $response->assertJson([
        'logo' => config('spa.logo', ''),
        'copyright' => config('spa.copyright', ''),
        'timeout' => config('spa.timeout', 3000),
        'title' => config('spa.title', 'Spa'),
        'company' => config('spa.company', 'ItStudio.at'),
    ]);
});
