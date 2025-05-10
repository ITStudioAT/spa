<?php

use App\Models\User;

it('can register_step_1: /api/admin/register_step_1', function () {

    $user = User::factory()->create([
        'email' => 'kron@naturwelt.at',
    ]);

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_EMAIL',
            'email' => 'example@naturwelt.at',
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_1', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'REGISTER_ENTER_TOKEN',
        ]);

    $data['data']['email'] = $user->email;
    $response = $this->postJson('/api/admin/register_step_1', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Registrieren funktioniert mit dieser E-Mail-Adresse nicht',
        ]);
});
