<?php

use App\Models\User;
use Tests\Controllers\AdminController\AdminControllerHelpers;

it('can password_unknown_step_1: /api/admin/password_unknown_step_1', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $response
        ->assertOk()
        ->assertJson([
            'step' => 'PASSWORD_UNKNOWN_ENTER_TOKEN',
        ]);
});

it('cant password_unknown_step_1, unknown E-Mail: /api/admin/password_unknown_step_1', function () {

    $email = 'xxx@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert mit dieser E-Mail-Adresse nicht',
        ]);
});

it('cant password_unknown_step_1, not confirmed: /api/admin/password_unknown_step_1', function () {

    $email = 'kron@naturwelt.at';

    $user = User::where('email', $email)->first();
    $user->confirmed_at = null;
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $response
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist noch nicht bestätigt',
        ]);
});

it('cant password_unknown_step_1, not active: /api/admin/password_unknown_step_1', function () {

    $email = 'kron@naturwelt.at';

    $user = User::where('email', $email)->first();
    $user->is_active = false;
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $response
        ->assertStatus(423)
        ->assertJson([
            'message' => 'Benutzer ist gesperrt',
        ]);
});
