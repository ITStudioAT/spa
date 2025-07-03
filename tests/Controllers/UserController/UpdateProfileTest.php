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


it('can test update: PUT /api/admin/users/update_profile', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $users = User::factory()->count(10)->create();

    $user = $users[0];

    $new_last_name = 'new_last_name';

    $data = [
        'id' => $user->id,
        'last_name' => $new_last_name,
        'first_name' => $user->first_name,
        'email' => $user->email,
        'is_2fa' => false
    ];


    $response = $this->putJson('/api/admin/users/update_profile/' . $user->id, $data)->assertOk();

    $this->assertEquals($user->id, $response->json('id'));
    $this->assertEquals($new_last_name, $response->json('last_name'));
    $this->assertEquals($user['first_name'], $response->json('first_name'));
    $this->assertEquals($user['email'], $response->json('email'));
    $this->assertEquals($user['is_active'], $response->json('is_active'));
    expect($response->json('is_2fa'))->toBeFalse();

    if ($response->json('is_confirmed')) {
        expect($user->confirmed_at->format('d.m.Y'))->toBe($response->json('confirmed_at'));
    } else {
        expect($response->json('confirmed_at'))->toBeNull();
    }

    if ($response->json('is_verified')) {
        expect($user->email_verified_at->format('d.m.Y'))->toBe($response->json('email_verified_at'));
    } else {
        expect($response->json('email_verified_at'))->toBeNull();
    }
});


it('cant test update, admin has no role: PUT /api/admin/users/update_profile', function () {

    $admin = User::factory()->create();
    Sanctum::actingAs($admin);


    $users = User::factory()->count(10)->create();

    $user = $users[0];

    $new_last_name = 'new_last_name';

    $data = [
        'id' => $user->id,
        'last_name' => $new_last_name,
        'first_name' => $user->first_name,
        'email' => $user->email,
        'is_2fa' => false
    ];


    $response = $this->putJson('/api/admin/users/update_profile/' . $user->id, $data)->assertStatus(403);
});

it('cant test update, new email already taken: PUT /api/admin/users/update_profile', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $users = User::factory()->count(10)->create();

    $user = $users[0];

    $new_last_name = 'new_last_name';

    $data = [
        'id' => $user->id,
        'last_name' => $new_last_name,
        'first_name' => $user->first_name,
        'email' => $admin->email,
        'is_2fa' => false
    ];


    $response = $this->putJson('/api/admin/users/update_profile/' . $user->id, $data)->assertStatus(422);
});


it('cant test update, email must be verified: PUT /api/admin/users/update_profile', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $users = User::factory()->count(10)->create();

    $user = $users[0];

    $new_last_name = 'new_last_name';
    $new_email = "hallo@itstudio.at";

    $data = [
        'id' => $user->id,
        'last_name' => $new_last_name,
        'first_name' => $user->first_name,
        'email' => $new_email,
        'is_2fa' => false
    ];


    $response = $this->putJson('/api/admin/users/update_profile/' . $user->id, $data)->assertOk()->assertJson([
        'answer' => 'INPUT_CODE',
        'email' => $user->email,
        'email_new' => $new_email,
    ]);
});
