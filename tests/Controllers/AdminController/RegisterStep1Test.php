<?php

use App\Models\User;

it('can register_step_1: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';
    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_EMAIL',
            'email' => $email,
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_1', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'REGISTER_ENTER_TOKEN',
        ]);

    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull();
    expect($user->register_started_at)->not->toBeNull();
});

it('cant register_step_1, email exists: /api/admin/register_step_1', function () {

    $email = 'kron@naturwelt.at';
    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_EMAIL',
            'email' => $email,
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_1', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Registrieren funktioniert mit dieser E-Mail-Adresse nicht.',
        ]);
});

it('cant register_step_1, wrong STEP: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';
    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_EMAILXXX',
            'email' => $email,
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_1', $data)
        ->assertStatus(422);
});
