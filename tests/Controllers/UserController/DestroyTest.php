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

    $this->user = User::factory()->create(
        [
            'is_active' => 1,
            'confirmed_at' => now()->addHour(),
            'email_verified_at' => now()->addHour(),
            'is_2fa' => 0,
        ]
    );
});

it('can test destroy: DELETE /api/admin/users/{user}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $response = $this->deleteJson('/api/admin/users/' . $this->user->id)
        ->assertNoContent();
});


it('cant test destroy, wrong role: DELETE /api/admin/users/{user}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $admin->syncRoles(['moderator']);

    $response = $this->deleteJson('/api/admin/users/' . $this->user->id)
        ->assertForbidden();
});

it('cant test destroy, cant delete yourself: DELETE /api/admin/users/{user}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

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

    $this->user->assignRole('admin');

    $response = $this->deleteJson('/api/admin/users/' . $this->user->id)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Benutzer kann nicht gelöscht werden, da er noch Rollen inne hat.',
        ]);
});
