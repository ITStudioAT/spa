<?php

use Itstudioat\Spa\Services\AdminNavigationService;

it('can AdminNavigationService::dashboardMenu /', function () {
    $service = new AdminNavigationService();

    $this
        ->get('/')
        ->assertOk()
        ->assertSee('Spa Homepage');
});
