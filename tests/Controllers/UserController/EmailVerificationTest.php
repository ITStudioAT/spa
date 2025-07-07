<?php

use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Composer\InstalledVersions;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Services\AdminNavigationService;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);

    // Überschreibt die bestehende Route im Testkontext:
    Route::get('/admin/email_verification', [UserController::class, 'emailVerification']);
    test()->withoutMiddleware();
});




it('can test EmailVerification: GET /admin/email_verification', function () {

    $user = User::factory()->create(['email_verified_at' => null]);
    $user->generateUuid();

    $data = [
        'email' => $user->email,
        'uuid' =>  (string)$user->uuid,
    ];

    $url = '/admin/email_verification?' . http_build_query($data);

    test()
        ->getJson($url)
        ->assertOk()
        ->assertJson([
            'result' => 'VERIFICATION_SUCCESS',
        ]);
});


it('cant test EmailVerification, wrong email: GET /admin/email_verification', function () {

    $user = User::factory()->create(['email_verified_at' => null]);
    $user->generateUuid();

    $data = [
        'email' => 'wrong@wrong.at',
        'uuid' =>  (string)$user->uuid,
    ];

    $url = '/admin/email_verification?' . http_build_query($data);

    test()
        ->getJson($url)
        ->assertStatus(422);
});

it('cant test EmailVerification, wrong uuid: GET /admin/email_verification', function () {

    $user = User::factory()->create(['email_verified_at' => null]);
    $user->generateUuid();

    $data = [
        'email' => $user->email,
        'uuid' =>  '123e4567-e89b-12d3-a456-426614174000',
    ];

    $url = '/admin/email_verification?' . http_build_query($data);

    test()
        ->getJson($url)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Die E-Mail-Verifikation hat nicht geklappt. Vermutlich ist die Zeit abgelaufen.',
        ]);
});


it('cant test EmailVerification, uuid_at expired: GET /admin/email_verification', function () {

    $user = User::factory()->create(['email_verified_at' => null]);
    $user->generateUuid();
    $user->uuid_at = now()->subMinutes(121); // Set uuid_at to expired time
    $user->save();

    $data = [
        'email' => $user->email,
        'uuid' =>  (string)$user->uuid,
    ];

    $url = '/admin/email_verification?' . http_build_query($data);

    test()
        ->getJson($url)
        ->assertStatus(403)
        ->assertJson([
            'message' => 'Die E-Mail-Verifikation hat nicht geklappt. Vermutlich ist die Zeit abgelaufen.',
        ]);
});
