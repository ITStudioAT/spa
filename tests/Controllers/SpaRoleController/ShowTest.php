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

it('can test show: GET /api/admin/roles/{role}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);


    $role = Role::create([
        'name' => 'test_role',
        'guard_name' => 'web',
    ]);

    $response = $this->getJson('/api/admin/roles/' . $role->id)->assertOk();

    $role = Role::find($role->id);
    $this->assertEquals($role->name, $response->json('name'));
    $this->assertEquals($role->id, $response->json('id'));
});

it('cant test show, no super_admin: GET /api/admin/roles/{role}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $role = Role::create([
        'name' => 'test_role',
        'guard_name' => 'web',
    ]);

    $response = $this->getJson('/api/admin/roles/' . $role->id)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Sie haben keine Berechtigung',
        ]);
});


it('cant test show, wrong role_id: GET /api/admin/roles/{role}', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);


    $role = Role::create([
        'name' => 'test_role',
        'guard_name' => 'web',
    ]);

    $response = $this->getJson('/api/admin/roles/999')
        ->assertStatus(404);
});
