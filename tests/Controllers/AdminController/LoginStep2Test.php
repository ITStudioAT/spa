<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {

    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
});


it('can login: /api/admin/login_step_2', function () {

    $password = "12345678";
    $users = User::factory()->count(1)->create(['password' => $password]);
    $user = $users[0];
    $user->assignRole('user');
    $user->is_2fa = false;
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => $user->email,
            'password' => $password,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'LOGIN_SUCCESS',
        ]);
});

it('cant login, wrong password: /api/admin/login_step_2', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('user');
    $user->is_2fa = false;
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => $user->email,
            'password' => $user->password,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Login funktioniert mit diesem Kennwort nicht.',
        ]);
});

it('cant login, wrong email: /api/admin/login_step_2', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('user');
    $user->is_2fa = false;
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => 'wrong@email.at',
            'password' => $user->password,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Login funktioniert mit dieser E-Mail-Adresse nicht.',
        ]);;
});

it('cant login, not confirmed: /api/admin/login_step_2', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('user');
    $user->is_2fa = false;
    $user->is_active = true;
    $user->confirmed_at = null;
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => $user->email,
            'password' => $user->password,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist noch nicht bestätigt.',
        ]);;
});

it('cant login, not active: /api/admin/login_step_2', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('user');
    $user->is_2fa = false;
    $user->is_active = false;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => $user->email,
            'password' => $user->password,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist gesperrt.',
        ]);;
});

it('must be 2_fa_login: /api/admin/login_step_2', function () {

    $password = "12345678";
    $users = User::factory()->count(1)->create(['password' => $password]);
    $user = $users[0];
    $user->assignRole('user');
    $user->is_2fa = true;
    $user->email_2fa = "user@email.at";
    $user->is_active = true;
    $user->confirmed_at = now();
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_PASSWORD',
            'email' => $user->email,
            'password' => $password,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'LOGIN_ENTER_TOKEN',
        ]);


    /*
    $response = $this->postJson('/api/admin/login_step_2', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'LOGIN_ENTER_TOKEN',
        ]);
        */
});
