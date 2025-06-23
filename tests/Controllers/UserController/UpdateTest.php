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

it('can test update: POST /api/admin/users/{user}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
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
    $user_id = $response->json('id');
    $user = [
        'id' => $user_id,
        'last_name' => 'Mustermann',
        'first_name' => 'Hirs',
        'email' => 'hirs@mustermann.at',
        'is_active' => 1,
        'is_confirmed' => 0,
        'is_verified' => 1,
        'is_2fa' => 0,
    ];

    $response = $this->putJson('/api/admin/users/' . $user_id, $user)->assertOk();

    $this->assertEquals($user_id, $response->json('id'));
    $this->assertEquals($user['last_name'], $response->json('last_name'));
    $this->assertEquals($user['first_name'], $response->json('first_name'));
    $this->assertEquals($user['email'], $response->json('email'));
    $this->assertEquals($user['is_active'], $response->json('is_active'));
    expect($response->json('email_verified_at'))->not->toBeNull();
    expect($response->json('confirmed_at'))->toBeNull();
    expect($response->json('is_2fa'))->toBeFalse();
});


it('cant test update, existing email: POST /api/admin/users/{user}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
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
    $user_id = $response->json('id');
    $user = [
        'id' => $user_id,
        'last_name' => 'Mustermann',
        'first_name' => 'Hirs',
        'email' => 'kron@naturwelt.at',
        'is_active' => 1,
        'is_confirmed' => 0,
        'is_verified' => 1,
        'is_2fa' => 0,
    ];

    $response = $this->putJson('/api/admin/users/' . $user_id, $user)->assertStatus(422);
});


it('cant test update, wrong role: POST /api/admin/users/{user}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
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
    $user_id = $response->json('id');
    $user = [
        'id' => $user_id,
        'last_name' => 'Mustermann',
        'first_name' => 'Hirs',
        'email' => 'kron@naturwelt.at',
        'is_active' => 1,
        'is_confirmed' => 0,
        'is_verified' => 1,
        'is_2fa' => 0,
    ];

    $admin->syncRoles(['moderator']);

    $response = $this->putJson('/api/admin/users/' . $user_id, $user)->assertForbidden();
});
