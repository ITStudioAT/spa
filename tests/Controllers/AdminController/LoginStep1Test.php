<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {

    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
});

it('can login: /api/admin/login_step_1', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('admin');

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_EMAIL',
            'email' => $user->email,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_1', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'LOGIN_ENTER_PASSWORD',
        ]);
});


it('cant login, no correct role: /api/admin/login_step_1', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_EMAIL',
            'email' => $user->email,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_1', $data)
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Login aufgrund der Berechtigungen nicht möglich.',
        ]);
});

it('cant login, wrong email: /api/admin/login_step_1', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('admin');

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_EMAIL',
            'email' => 'wrong@email.at',
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_1', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Login funktioniert mit dieser E-Mail-Adresse nicht.',
        ]);
});

it('cant login, not confirmed: /api/admin/login_step_1', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('admin');
    $user->confirmed_at = null;
    $user->save();

    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_EMAIL',
            'email' => $user->email,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_1', $data)
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist noch nicht bestätigt.',
        ]);

    $user->confirmed_at = now();
    $user->save();
});

it('cant login, not active: /api/admin/login_step_1', function () {

    $users = User::factory()->count(1)->create();
    $user = $users[0];
    $user->assignRole('admin');
    $user->is_active = false;
    $user->save();


    $data = [
        'data' => [
            'step' => 'LOGIN_ENTER_EMAIL',
            'email' => $user->email,
        ],
    ];

    $response = $this->postJson('/api/admin/login_step_1', $data)
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist gesperrt.',
        ]);

    $user->is_active = true;
    $user->save();
});
