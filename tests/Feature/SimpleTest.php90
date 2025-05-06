<?php


use Illuminate\Console\Command;

use Itstudioat\Spa\Commands\SpaUpdate;
use function Pest\Laravel\artisan;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can test', function () {

    $response = getJson('/api/admin/config');
    $response->assertOk();
});
