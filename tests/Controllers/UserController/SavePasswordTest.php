<?php

use App\Models\User;
use App\Services\AdminNavigationService;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
});


it('can test savePassword: POST /api/admin/users/save_password', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';

    $data = [
        'password' => $new_password,
        'password_repeat' => $new_password
    ];

    $response = $this->postJson('/api/admin/users/save_password', $data)
        ->assertOk()->assertJson([
            'step' => 'PASSWORD_ENTER_TOKEN',
        ]);
});


it('cant test savePassword, admin has no role: POST /api/admin/users/save_password', function () {

    $admin = User::factory()->create();
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';

    $data = [
        'password' => $new_password,
        'password_repeat' => $new_password
    ];

    $response = $this->postJson('/api/admin/users/save_password', $data)
        ->assertStatus(403);
});


it('cant test savePassword, password and password_repeat not the same: POST /api/admin/users/save_password', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';

    $data = [
        'password' => $new_password,
        'password_repeat' => '12345679'
    ];

    $response = $this->postJson('/api/admin/users/save_password', $data)
        ->assertStatus(422);
});


it('cant test savePassword, password too shot: POST /api/admin/users/save_password', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '123456';

    $data = [
        'password' => $new_password,
        'password_repeat' => $new_password,
    ];

    $response = $this->postJson('/api/admin/users/save_password', $data)
        ->assertStatus(422);
});
