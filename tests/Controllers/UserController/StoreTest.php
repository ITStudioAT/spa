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

it('can test store: POST /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    $user = [
        'last_name' => 'Mustermann',
        'first_name' => 'Max',
        'email' => 'max@mustermann.at',
        'is_active' => 1,
        'is_confirmed' => 1,
        'is_verified' => 1,
        'is_2fa' => 0,
    ];

    $response = $this->postJson('/api/admin/users', $user)->assertOk();

    $response->assertJson([
        'last_name' => $user['last_name'],
        'first_name' => $user['first_name'],
        'email' => $user['email'],
        'is_active' => $user['is_active'],
        'is_confirmed' => $user['is_confirmed'],
        'is_verified' => $user['is_verified'],
        'is_2fa' => $user['is_2fa'],
    ]);
});

it('cant test store, wrong role: POST /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('moderator');
    Sanctum::actingAs($admin);

    $user = [
        'last_name' => 'Mustermann',
        'first_name' => 'Max',
        'email' => 'max@mustermann.at',
        'is_active' => 1,
        'is_confirmed' => 1,
        'is_verified' => 1,
        'is_2fa' => 0,
    ];

    $response = $this->postJson('/api/admin/users', $user)
        ->assertStatus(403);
});

it('cant test store, email exists: POST /api/admin/users', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = [
        'last_name' => 'Mustermann',
        'first_name' => 'Max',
        'email' => 'kron@naturwelt.at',
        'is_active' => 1,
        'is_confirmed' => 1,
        'is_verified' => 1,
        'is_2fa' => 0,
    ];

    $response = $this->postJson('/api/admin/users', $user)
        ->assertStatus(422);
});
