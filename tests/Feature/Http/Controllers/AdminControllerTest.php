<?php

use Itstudioat\Spa\Http\Controllers\AdminController;

it('has a route', function () {
    $this->get(action([AdminController::class, 'index']))
        ->assertOk()
        ->assertSee('ok');
});
