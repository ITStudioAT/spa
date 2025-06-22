<?php

use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\DB;
use App\Services\AdminNavigationService;
use Illuminate\Database\Eloquent\Relations\Relation;

beforeEach(function () {

    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    DB::table('model_has_roles')
        ->where('model_type', 'Illuminate\Foundation\Auth\User')
        ->update(['model_type' => 'App\Models\User']);
});

it('can test destroy: DELETE /api/admin/roles/{role}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);


    $role = Role::create([
        'name' => 'test_role',
        'guard_name' => 'web',
    ]);

    $response = $this->deleteJson('/api/admin/roles/' . $role->id)
        ->assertStatus(204);

    $this->assertDatabaseMissing('roles', [
        'id' => $role->id,
    ]);
});

it('cant test destroy, user not super_admin: DELETE /api/admin/roles/{role}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $role = Role::create([
        'name' => 'test_role',
        'guard_name' => 'web',
    ]);

    $response = $this->deleteJson('/api/admin/roles/' . $role->id)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Sie haben keine Berechtigung',
        ]);
});

it('cant test destroy, role has depended users: DELETE /api/admin/roles/{role}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $role = Role::where('name', 'admin')->first();

    $response = $this->deleteJson('/api/admin/roles/' . $role->id)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Beim Löschen ist ein Fehler aufgetreten. Möglicherweise gibt es abhängige Daten.',
        ]);
});
