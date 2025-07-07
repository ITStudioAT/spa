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


it('can test Save2Fa: POST /api/admin/users/save_2fa', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at'
    ];


    $response = $this->postJson('/api/admin/users/save_2fa', $data)->assertOk()->assertJson([
        'result' => 'TWO_FA_EMAIL_IS_NEW',
    ]);
});


it('cant test Save2Fa, wrong user_id: POST /api/admin/users/save_2fa', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $data = [
        'id' => 99999,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at'
    ];


    $response = $this->postJson('/api/admin/users/save_2fa', $data)->assertStatus(422);
});

it('cant test Save2Fa, admin has no role: POST /api/admin/users/save_2fa', function () {

    $admin = User::factory()->create();
    Sanctum::actingAs($admin);


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at'
    ];


    $response = $this->postJson('/api/admin/users/save_2fa', $data)->assertStatus(403);
});


it('cant test Save2Fa, existing, but wrong user_id: POST /api/admin/users/save_2fa', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $data = [
        'id' => 1,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at'
    ];


    $response = $this->postJson('/api/admin/users/save_2fa', $data)->assertStatus(403)->assertJson([
        'message' => 'Sie haben keine Berechtigung',
    ]);
});


it('cant test Save2Fa, equal email_2fa and email: POST /api/admin/users/save_2fa', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => $admin->email,
    ];


    $response = $this->postJson('/api/admin/users/save_2fa', $data)->assertStatus(422)->assertJson([
        'message' => 'Die E-Mail und die E-Mail für die 2-Faktoren-Authentifizierung dürfen nicht gleich sein',
    ]);
});


it('cant test Save2Fa, email_2fa empty: POST /api/admin/users/save_2fa', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => '',
    ];


    $response = $this->postJson('/api/admin/users/save_2fa', $data)->assertStatus(422);
});
