<?php

it('can render the homepage /', function () {
    $this
        ->get('/')
        ->assertOk()
        ->assertSee('Spa Homepage');
});
