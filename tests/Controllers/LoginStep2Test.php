<?php

use App\Models\User;

it('can login: /api/admin/login_step_2', function () {

    $user = User::find(1);
    $user->is_2fa = false;
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
            'step' => 'LOGIN_SUCCESS',
        ]);
});

it('cant login, wrong password: /api/admin/login_step_2', function () {

    $user = User::find(1);
    $user->is_2fa = false;
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => 'kron@naturwelt.at',
            'password' => 'wrong_password123',
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Login funktioniert mit diesem Kennwort nicht.',
        ]);
});

it('cant login, wrong email: /api/admin/login_step_2', function () {

    $user = User::find(1);
    $user->is_2fa = false;
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => 'wrong@naturwelt.at',
            'password' => 'wrong_password123',
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Login funktioniert mit dieser E-Mail-Adresse nicht.',
        ]);;
});

it('cant login, not confirmed: /api/admin/login_step_2', function () {

    $user = User::find(1);
    $user->is_2fa = false;
    $user->is_active = true;
    $user->confirmed_at = null;
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => 'kron@naturwelt.at',
            'password' => 'password123',
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist noch nicht bestätigt.',
        ]);;
});

it('cant login, not active: /api/admin/login_step_2', function () {

    $user = User::find(1);
    $user->is_2fa = false;
    $user->is_active = false;
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
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist gesperrt.',
        ]);;
});

it('must be 2_fa_login: /api/admin/login_step_2', function () {

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
});
