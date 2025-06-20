<?php

use App\Models\User;
use Tests\Controllers\AdminController\AdminControllerHelpers;

it('can password_unknown_step_2: /api/admin/password_unknown_step_2', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $user->is_2fa = false;
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);

    $response
        ->assertOk()
        ->assertJson([
            'step' => 'PASSWORD_UNKNOWN_SUCCESS',
        ]);
});

it('cant password_unknown_step_2, token expired: /api/admin/password_unknown_step_2', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $user->is_2fa = false;
    $user->token_2fa_expires_at = now()->subMinute();
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_2, wrong token: /api/admin/password_unknown_step_2', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = "123456";

    $user->is_2fa = false;
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});


it('can password_unknown_step_2 with 2fa: /api/admin/password_unknown_step_2', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $response
        ->assertOk()
        ->assertJson([
            'step' => 'PASSWORD_UNKNOWN_ENTER_TOKEN',
        ]);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $user->is_2fa = true;
    $user->email_2fa = "second@kronsoft.at";
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);

    $response
        ->assertOk()
        ->assertJson([
            'step' => 'PASSWORD_UNKNOWN_ENTER_TOKEN_2',
        ]);
});

it('cant password_unknown_step_2 with 2fa, but no 2nd email: /api/admin/password_unknown_step_2', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $response
        ->assertOk()
        ->assertJson([
            'step' => 'PASSWORD_UNKNOWN_ENTER_TOKEN',
        ]);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $user->is_2fa = true;
    $user->email_2fa = null;
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Sie haben keine weitere E-Mail-Adresse.',
        ]);
});
