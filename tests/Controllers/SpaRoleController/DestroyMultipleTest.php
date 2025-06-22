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

it('can test destroyMultiple: POST /api/admin/roles/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    $role_1 = Role::firstOrCreate(['name' => 'test_1', 'guard_name' => 'web']);
    $role_2 = Role::firstOrCreate(['name' => 'test_2', 'guard_name' => 'web']);
    $role_3 = Role::firstOrCreate(['name' => 'test_3', 'guard_name' => 'web']);


    $data = [$role_1->id, $role_2->id, $role_3->id];

    $response = $this->postJson('/api/admin/roles/destroy_multiple', [
        $data,
    ]);

    $response = $this->postJson('/api/admin/roles/destroy_multiple', $data)
        ->assertStatus(204);

    $this->assertDatabaseMissing('roles', ['id' => $role_1->id]);
    $this->assertDatabaseMissing('roles', ['id' => $role_2->id]);
    $this->assertDatabaseMissing('roles', ['id' => $role_3->id]);
});

it('cant test destroyMultiple, user no super_admin: POST /api/admin/roles/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $role_1 = Role::firstOrCreate(['name' => 'test_1', 'guard_name' => 'web']);
    $role_2 = Role::firstOrCreate(['name' => 'test_2', 'guard_name' => 'web']);
    $role_3 = Role::firstOrCreate(['name' => 'test_3', 'guard_name' => 'web']);


    $data = [$role_1->id, $role_2->id, $role_3->id];

    $response = $this->postJson('/api/admin/roles/destroy_multiple', [
        $data,
    ]);

    $response = $this->postJson('/api/admin/roles/destroy_multiple', $data)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Sie haben keine Berechtigung',
        ]);
});


it('can test destroyMultiple, although wrong id in array: POST /api/admin/roles/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    Sanctum::actingAs($admin);

    $role_1 = Role::firstOrCreate(['name' => 'test_1', 'guard_name' => 'web']);
    $role_2 = Role::firstOrCreate(['name' => 'test_2', 'guard_name' => 'web']);
    $role_3 = Role::firstOrCreate(['name' => 'test_3', 'guard_name' => 'web']);


    $data = [$role_1->id, 999, $role_3->id];

    $response = $this->postJson('/api/admin/roles/destroy_multiple', [
        $data,
    ]);

    $response = $this->postJson('/api/admin/roles/destroy_multiple', $data)
        ->assertStatus(204);

    $this->assertDatabaseMissing('roles', ['id' => $role_1->id]);
    $this->assertDatabaseHas('roles', ['id' => $role_2->id]);
    $this->assertDatabaseMissing('roles', ['id' => $role_3->id]);
});

it('cant test destroyMultiple, role has depending users: POST /api/admin/roles/destroy_multiple', function () {

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $role_1 = Role::firstOrCreate(['name' => 'test_1', 'guard_name' => 'web']);
    $role_2 = Role::where('name', 'admin')->first();
    $role_3 = Role::firstOrCreate(['name' => 'test_3', 'guard_name' => 'web']);


    $data = [$role_1->id, $role_2->id, $role_3->id];

    $response = $this->postJson('/api/admin/roles/destroy_multiple', [
        $data,
    ]);

    $response = $this->postJson('/api/admin/roles/destroy_multiple', $data)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Beim Löschen ist ein Fehler aufgetreten. Möglicherweise gibt es abhängige Daten.',
        ]);
});
