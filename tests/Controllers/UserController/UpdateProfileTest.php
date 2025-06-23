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

   /*
it('can test update: PUT /api/users/update_profile', function () {

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    dump($user);

 
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
  */
