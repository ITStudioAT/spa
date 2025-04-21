<?php

use Tests\Models\User;
use Illuminate\Console\Command;
use function Pest\Laravel\artisan;
use Itstudioat\Spa\Commands\CreateUser;



it('executes CreateUserCommand', function () {
    artisan(CreateUser::class)
        ->expectsQuestion('Vorname', 'Günther')
        ->expectsQuestion('Nachname', 'Kron')
        ->expectsQuestion('E-Mail', 'kron@naturwelt.at')
        ->expectsQuestion('Passwort (wird ausgeblendet)', '12345678')
        ->assertExitCode(Command::SUCCESS);
});

/*
it('loads the path /admin', function () {
    $user = User::factory()->create();

    // Act as the created user (authenticates the user for the test)
    $this->actingAs($user);

    // Test the route
    $response = $this->get('/admin');
    $response->assertOk();
});
*/
