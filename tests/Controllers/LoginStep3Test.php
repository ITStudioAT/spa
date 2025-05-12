<?php

use App\Models\User;

it('can login: /api/admin/login_step_3', function () {

    $user = User::find(1);
    $user->is_2fa = true;
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => 'kron@naturwelt.at',
            'password' => 'password123',
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'LOGIN_ENTER_TOKEN',
        ]);;


    $user = User::find(1);
    $token_2fa = $user->token_2fa;

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_TOKEN',
            'email' => 'kron@naturwelt.at',
            'password' => 'password123',
            'token_2fa' => $token_2fa,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_3', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'LOGIN_SUCCESS',
        ]);
});


it('cant login, wrong token: /api/admin/login_step_3', function () {

    $user = User::find(1);
    $user->is_2fa = true;
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => 'kron@naturwelt.at',
            'password' => 'password123',
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'LOGIN_ENTER_TOKEN',
        ]);;


    $user = User::find(1);
    $token_2fa = $user->token_2fa;

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_TOKEN',
            'email' => 'kron@naturwelt.at',
            'password' => 'password123',
            'token_2fa' => '123456',
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_3', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Login funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});
