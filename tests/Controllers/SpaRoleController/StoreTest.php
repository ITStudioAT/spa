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
});

it('can test store: POST /api/admin/roles', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    $name = 'new_role';

    $response = $this->postJson('/api/admin/roles', [
        'name' => $name
    ])->assertOk();


    $role = Role::where('name', $name)->first();
    $this->assertEquals($role->name, $response->json('name'));
    $this->assertEquals($role->id, $response->json('id'));
});

it('canr test store, no super_admin: POST /api/admin/roles', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $name = 'new_role';

    $response = $this->postJson('/api/admin/roles', [
        'name' => $name
    ])->assertStatus(403)
        ->assertJson([
            'message' => 'Sie haben keine Berechtigung',
        ]);
});

it('cant test store, empty role: POST /api/admin/roles', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    $name = null;

    $response = $this->postJson('/api/admin/roles', [
        'name' => $name
    ])->assertStatus(422);
});

it('cant test store, role_name exists: POST /api/admin/roles', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    $name = 'admin';

    $response = $this->postJson('/api/admin/roles', [
        'name' => $name
    ])->assertStatus(422);
});
