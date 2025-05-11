<?php

use App\Models\User;

it('can register with new email: /api/admin/register_step_1', function () {

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
});

it('can not register with existing email: /api/admin/register_step_1', function () {
    $user = User::factory()->create([
        'email' => 'kron@naturwelt.at',
    ]);

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_EMAIL',
            'email' => 'kron@naturwelt.at',
        ],
    ];

    $data['data']['email'] = $user->email;
    $response = $this->postJson('/api/admin/register_step_1', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Registrieren funktioniert mit dieser E-Mail-Adresse nicht',
        ]);
});
