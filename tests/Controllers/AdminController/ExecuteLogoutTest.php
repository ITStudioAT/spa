<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);

    $this->user = User::factory()->create(
        [
            'is_active' => 1,
            'confirmed_at' => now()->addHour(),
            'email_verified_at' => now()->addHour(),
            'is_2fa' => 0,
        ]
    );
});


it('can logout: /api/admin/execute_logout', function () {

    $admin = $this->user;
    $admin->assignRole('admin');

    $this->withMiddleware('web')
        ->actingAs($admin, 'web')
        ->postJson('/api/admin/execute_logout')
        ->assertOk()
        ->assertJson(['message' => 'Logout successful']);
});


it('cant logout, not logged in: /api/admin/execute_logout', function () {

    $response = $this->postJson('/api/admin/execute_logout')
        ->assertStatus(401);
});
