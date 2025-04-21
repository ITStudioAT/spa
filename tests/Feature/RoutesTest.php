<?php

use Tests\Models\User;



it('loads the path /', function () {
    $response = $this->get('/');
    $response->assertOk();
});

it('loads the path /admin', function () {
    $user = User::factory()->create();

    // Act as the created user (authenticates the user for the test)
    $this->actingAs($user);

    // Test the route
    $response = $this->get('/admin');
    $response->assertOk();
});
