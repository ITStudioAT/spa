<?php

it('can render the homepage /', function () {
    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('Spa Homepage');
});
