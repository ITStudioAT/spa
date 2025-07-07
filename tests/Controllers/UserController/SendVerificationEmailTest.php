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




it('can test SendVerificationEmail with 1 id: POST /api/admin/users/send_verification_email', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory(10)->create();

    $user = User::find($users[0]->id);
    $user->email_verified_at = null;
    $user->uuid = null;
    $user_uuid_at = null;
    $user->save();

    $data = [
        'ids' => [$users[0]->id],
    ];

    $response = test()->postJson('/api/admin/users/send_verification_email', $data)->assertOk()->assertJson([
        'result' => 'EMAIL_SENT',
    ]);

    $users[0]->refresh();
    expect($users[0]->uuid)->not->toBeNull();
    expect($users[0]->uuid_at)->not->toBeNull();
});


it('can test SendVerificationEmail with 10 ids: POST /api/admin/users/send_verification_email', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $users = User::factory(10)->create();

    foreach ($users as $user) {
        $user->email_verified_at = null;
        $user->uuid = null;
        $user->uuid_at = null;
        $user->save();
    }

    $data = [
        'ids' => $users->pluck('id')->all(),
    ];

    $response = test()->postJson('/api/admin/users/send_verification_email', $data)->assertOk()->assertJson([
        'result' => 'EMAIL_SENT',
    ]);

    foreach ($data['ids'] as $id) {
        $user = User::find($id);
        expect($user->uuid)->not->toBeNull();
        expect($user->uuid_at)->not->toBeNull();
    }
});


it('cant test SendVerificationEmail, admin has wrong role: POST /api/admin/users/send_verification_email', function () {

    $admin = User::factory()->create();
    $admin->assignRole('moderator');
    Sanctum::actingAs($admin);

    $users = User::factory(10)->create();

    foreach ($users as $user) {
        $user->email_verified_at = null;
        $user->uuid = null;
        $user->uuid_at = null;
        $user->save();
    }

    $data = [
        'ids' => $users->pluck('id')->all(),
    ];

    $response = test()->postJson('/api/admin/users/send_verification_email', $data)->assertStatus(403);
});


it('cant test SendVerificationEmail, array of ids has one false id: POST /api/admin/users/send_verification_email', function () {

    $admin = User::factory()->create();
    $admin->assignRole('moderator');
    Sanctum::actingAs($admin);

    $users = User::factory(10)->create();

    foreach ($users as $user) {
        $user->email_verified_at = null;
        $user->uuid = null;
        $user->uuid_at = null;
        $user->save();
    }

    $valid_ids = $users->pluck('id')->all();
    $invalid_id = User::max('id') + 1; // garantiert ungültig

    $data = [
        'ids' => array_merge($valid_ids, [$invalid_id]),
    ];

    $response = test()->postJson('/api/admin/users/send_verification_email', $data)->assertStatus(403);
});
