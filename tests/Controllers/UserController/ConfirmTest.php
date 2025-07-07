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




it('can test Confirm with 1 id: POST /api/admin/users/confirm', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory(10)->create();

    $user = User::find($users[0]->id);
    $user->confirmed_at = null;
    $user->save();

    $data = [
        'ids' => [$users[0]->id],
    ];

    $response = test()->postJson('/api/admin/users/confirm', $data)->assertOk()->assertJson([
        'result' => 'EMAIL_SENT',
    ]);

    $users[0]->refresh();

    // Test: confirmed_at darf **nicht** null sein
    expect($users[0]->confirmed_at)->not->toBeNull();
});

it('can test Confirm with all 10 ids: POST /api/admin/users/confirm', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory(10)->create();

    $data = [
        'ids' => $users->pluck('id')->all(),
    ];

    $response = test()->postJson('/api/admin/users/confirm', $data)->assertOk()->assertJson([
        'result' => 'EMAIL_SENT',
    ]);

    // Benutzer aus der Datenbank neu laden und alle prüfen
    foreach ($data['ids'] as $id) {
        $user = User::find($id);
        expect($user->confirmed_at)->not->toBeNull();
    }
});

it('cant test Confirm, admin wrong role: POST /api/admin/users/confirm', function () {

    $admin = User::factory()->create();
    $admin->assignRole('moderator');
    Sanctum::actingAs($admin);

    $users = User::factory(10)->create();

    $data = [
        'ids' => $users->pluck('id')->all(),
    ];

    $response = test()->postJson('/api/admin/users/confirm', $data)->assertStatus(403);
});
