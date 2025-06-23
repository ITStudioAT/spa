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

it('can test destroy: DELETE /api/admin/users/{user}', function () {

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

    $response = $this->deleteJson('/api/admin/users/' . $user_id)
        ->assertNoContent();
});


it('cant test destroy, wrong role: DELETE /api/admin/users/{user}', function () {

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

    $response = $this->deleteJson('/api/admin/users/' . $user_id)
        ->assertForbidden();
});

it('cant test destroy, cant delete yourself: DELETE /api/admin/users/{user}', function () {

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

    $response = $this->deleteJson('/api/admin/users/' . $admin->id)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Man kann sich selbst nicht löschen',
        ]);
});

it('cant test destroy, cant delete users with roles: DELETE /api/admin/users/{user}', function () {

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
    $user = User::find($user_id);
    $user->assignRole('admin');

    $response = $this->deleteJson('/api/admin/users/' . $user->id)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Benutzer kann nicht gelöscht werden, da er noch Rollen inne hat.',
        ]);
});
