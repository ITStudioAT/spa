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


function save2Fa($admin)
{

    $email_2fa = 'hallo@itstudio.at';


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => $email_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa', $data)->assertOk()->assertJson([
        'result' => 'TWO_FA_EMAIL_IS_NEW',
    ]);

    $admin->email_2fa = $email_2fa;
    $admin->email_2fa_verified_at = now();
    $admin->save();
}

it('can test Save2FaWithCode: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);

    $token_2fa = $admin->token_2fa;
    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at',
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertOk()->assertJson([
        'result' => 'TWO_FA_SET',
    ]);
});


it('cant test Save2FaWithCode, admin has no role: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $admin->syncRoles();
    Sanctum::actingAs($admin);

    $token_2fa = $admin->token_2fa;
    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at',
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(403);
});



it('cant test Save2FaWithCode, not existing user_id: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $token_2fa = $admin->token_2fa;

    $data = [
        'id' => 99999,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at',
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(422);
});



it('cant test Save2FaWithCode, existing, but wrong user_id: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $token_2fa = $admin->token_2fa;

    $data = [
        'id' => 1,
        'is_2fa' => true,
        'email_2fa' => 'hallo@itstudio.at',
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(403)->assertJson([
        'message' => 'Sie haben keine Berechtigung',
    ]);
});

it('cant test Save2FaWithCode, is_2fa == false: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $token_2fa = $admin->token_2fa;

    $data = [
        'id' => $admin->id,
        'is_2fa' => false,
        'email_2fa' => 'hallo@itstudio.at',
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(422)->assertJson([
        'message' => 'Keine 2-Faktoren-Authentifizierung gewünscht',
    ]);
});


it('cant test Save2FaWithCode, email and email_2fa must not be the same: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $token_2fa = $admin->token_2fa;

    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => $admin->email,
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(422)->assertJson([
        'message' => 'Die E-Mail und die E-Mail für die 2-Faktoren-Authentifizierung dürfen nicht gleich sein',
    ]);
});



it('cant test Save2FaWithCode, email_2fa is not verified: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $token_2fa = $admin->token_2fa;

    $admin->email_2fa_verified_at = null;
    $admin->save();


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => $admin->email_2fa,
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(422)->assertJson([
        'message' => 'Die E-Mail für die 2-Faktoren-Authentifizierung ist nicht verifiziert',
    ]);
});


it('cant test Save2FaWithCode, wrong token: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $token_2fa = $admin->token_2fa;


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => $admin->email_2fa,
        'token_2fa' => '123456'
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(401)->assertJson([
        'message' => 'Der Code ist falsch oder abgelaufen',
    ]);
});


it('cant test Save2FaWithCode, token expired: POST /api/admin/users/save_2fa_with_code', function () {

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Sanctum::actingAs($admin);

    save2Fa($admin);

    $admin = User::find($admin->id);
    $admin->token_2fa_expires_at = now()->subMinutes(120); // Set token to expired
    $admin->save();
    $token_2fa = $admin->token_2fa;


    $data = [
        'id' => $admin->id,
        'is_2fa' => true,
        'email_2fa' => $admin->email_2fa,
        'token_2fa' => $token_2fa
    ];

    $response = test()->postJson('/api/admin/users/save_2fa_with_code', $data)->assertStatus(401)->assertJson([
        'message' => 'Der Code ist falsch oder abgelaufen',
    ]);
});
