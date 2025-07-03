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


function savePassword($new_password)
{
    $data = [
        'password' => $new_password,
        'password_repeat' => $new_password
    ];

    $response = test()->postJson('/api/admin/users/save_password', $data)
        ->assertOk()->assertJson([
            'step' => 'PASSWORD_ENTER_TOKEN',
        ]);

    return $data;
}


it('can test savePassword: POST /api/admin/users/save_password_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';
    $data = savePassword($new_password);

    $user = User::find($user->id);
    $data['token_2fa'] = $user->token_2fa;

    $response = test()->postJson('/api/admin/users/save_password_with_code', $data)
        ->assertStatus(204);
});


it('cant test savePassword, admin has no role: POST /api/admin/users/save_password_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';
    $data = savePassword($new_password);

    $user = User::find($user->id);
    $data['token_2fa'] = $user->token_2fa;

    $admin->syncRoles();

    $response = test()->postJson('/api/admin/users/save_password_with_code', $data)
        ->assertStatus(403);
});


it('cant test savePassword, password and password_repeat not the same: POST /api/admin/users/save_password_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';
    $data = savePassword($new_password);

    $user = User::find($user->id);
    $data['token_2fa'] = $user->token_2fa;
    $data['password_repeat'] = '87654321'; // Not the same as password



    $response = test()->postJson('/api/admin/users/save_password_with_code', $data)
        ->assertStatus(422);
});


it('cant test savePassword, password too short: POST /api/admin/users/save_password_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';
    $data = savePassword($new_password);

    $user = User::find($user->id);
    $data['token_2fa'] = $user->token_2fa;
    $data['password'] = '1234';
    $data['password_repeat'] = '1234';



    $response = test()->postJson('/api/admin/users/save_password_with_code', $data)
        ->assertStatus(422);
});

it('cant test savePassword, wrong token: POST /api/admin/users/save_password_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';
    $data = savePassword($new_password);

    $user = User::find($user->id);
    $data['token_2fa'] = '123456'; // Wrong token, should be 6 digits

    $response = test()->postJson('/api/admin/users/save_password_with_code', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort speichern funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});


it('cant test savePassword, token_2fa expired: POST /api/admin/users/save_password_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('user');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_password = '12345678';
    $data = savePassword($new_password);

    $user = User::find($user->id);
    $user->token_2fa_expires_at = $user->token_2fa_expires_at->subMinutes(120); // Set token to expired
    $user->save();
    $data['token_2fa'] = $user->token_2fa;

    $response = test()->postJson('/api/admin/users/save_password_with_code', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort speichern funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});
