<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Spatie\Permission\Models\Role;
use App\Services\AdminNavigationService;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);

    date_default_timezone_set('Europe/Vienna');
    Config::set('spa.token_expire_time', 120);
});


function updateProfile($user, $new_email)
{
    $data = [
        'id' => $user->id,
        'last_name' => $user->last_name,
        'first_name' => $user->first_name,
        'email' => $new_email,
        'is_2fa' => false,
    ];

    $response = test()->putJson('/api/admin/users/update_profile/' . $user->id, $data)->assertOk()->assertJson([
        'answer' => 'INPUT_CODE',
        'email' => $user->email,
        'email_new' => $new_email,
    ]);

    return $data;
}


it('can test update: POST /api/admin/users/update_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_email = 'hallo@itstudio.at';
    $data = updateProfile($user, $new_email);

    $user = User::find($user->id);
    $data['token_2fa'] = $user->token_2fa;


    $response = $this->postJson('/api/admin/users/update_with_code', $data)->assertOk()->assertJson([
        'email' => $new_email,
    ]);
});

it('cant test update, admin has no role: POST /api/admin/users/update_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_email = 'hallo@itstudio.at';
    $data = updateProfile($user, $new_email);

    $user = User::find($user->id);
    $data['token_2fa'] = $user->token_2fa;


    $admin->syncRoles();

    $response = $this->postJson('/api/admin/users/update_with_code', $data)->assertStatus(403);
});


it('cant test update, wrong 2fa_code: POST /api/admin/users/update_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_email = 'hallo@itstudio.at';
    $data = updateProfile($user, $new_email);

    $user = User::find($user->id);
    $data['token_2fa'] = '123456';

    $response = $this->postJson('/api/admin/users/update_with_code', $data)->assertStatus(401)->assertJson([
        'message' => 'Der Code ist falsch oder abgelaufen',
    ]);
});


it('cant test update, 2fa_code expired: POST /api/admin/users/update_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    $user = $admin;

    $new_email = 'hallo@itstudio.at';
    $data = updateProfile($user, $new_email);

    $user = User::find($user->id);
    $user->token_2fa_expires_at = now()->subMinutes(120); // Set the token to be expired
    $user->save();
    $data['token_2fa'] = $user->token_2fa;

    $response = $this->postJson('/api/admin/users/update_with_code', $data)->assertStatus(401)->assertJson([
        'message' => 'Der Code ist falsch oder abgelaufen',
    ]);
});
