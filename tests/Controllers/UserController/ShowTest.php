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

it('can test show: GET /api/admin/users/{user}', function () {

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

    $response = $this->getJson('/api/admin/users/' . $user_id)
        ->assertOk();



    $this->assertEquals($user_id, $response->json('id'));
    $this->assertEquals($user['last_name'], $response->json('last_name'));
    $this->assertEquals($user['first_name'], $response->json('first_name'));
    $this->assertEquals($user['email'], $response->json('email'));
    $this->assertEquals($user['is_active'], $response->json('is_active'));
    expect($response->json('email_verified_at'))->not->toBeNull();
    expect($response->json('confirmed_at'))->not->toBeNull();
    expect($response->json('is_2fa'))->toBeFalse();
});

it('cant test show, wrong role: GET /api/admin/users/{user}', function () {

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


    $admin->syncRoles(['moderator']);
    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/admin/users/' . $user_id)
        ->assertForbidden();
});
