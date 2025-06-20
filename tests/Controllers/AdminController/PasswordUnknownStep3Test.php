<?php

use App\Models\User;
use Tests\Controllers\AdminController\AdminControllerHelpers;

it('can password_unknown_step_3: /api/admin/password_unknown_step_3', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;
    $user->is_2fa = true;
    $user->email_2fa = "second@kronsoft.at";
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);
    $user = User::where('email', $email)->first();
    $token_2fa_2 = $user->token_2fa_2;

    $response = AdminControllerHelpers::passwordUnknownStep3($email, $token_2fa, $token_2fa_2);

    $response
        ->assertOk()
        ->assertJson([
            'step' => 'PASSWORD_UNKNOWN_ENTER_PASSWORD',
        ]);
});

it('cant password_unknown_step_3, wrong token_2fa: /api/admin/password_unknown_step_3', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;
    $user->is_2fa = true;
    $user->email_2fa = "second@kronsoft.at";
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);
    $user = User::where('email', $email)->first();
    $token_2fa_2 = $user->token_2fa_2;

    $token_2fa = "123456";

    $response = AdminControllerHelpers::passwordUnknownStep3($email, $token_2fa, $token_2fa_2);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_3, wrong token_2fa_2: /api/admin/password_unknown_step_3', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;
    $user->is_2fa = true;
    $user->email_2fa = "second@kronsoft.at";
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);
    $user = User::where('email', $email)->first();
    $token_2fa_2 = $user->token_2fa_2;

    $token_2fa_2 = "123456";

    $response = AdminControllerHelpers::passwordUnknownStep3($email, $token_2fa, $token_2fa_2);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_3, token_2fa expired: /api/admin/password_unknown_step_3', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;
    $user->is_2fa = true;
    $user->email_2fa = "second@kronsoft.at";
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);
    $user = User::where('email', $email)->first();
    $token_2fa_2 = $user->token_2fa_2;

    $user->token_2fa_expires_at = now()->subMinute();
    $user->save();


    $response = AdminControllerHelpers::passwordUnknownStep3($email, $token_2fa, $token_2fa_2);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_3, token_2fa_2 expired: /api/admin/password_unknown_step_3', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;
    $user->is_2fa = true;
    $user->email_2fa = "second@kronsoft.at";
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);
    $user = User::where('email', $email)->first();
    $token_2fa_2 = $user->token_2fa_2;

    $user->token_2fa_2_expires_at = now()->subMinute();
    $user->save();


    $response = AdminControllerHelpers::passwordUnknownStep3($email, $token_2fa, $token_2fa_2);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});


it('cant password_unknown_step_3, not is_2fa: /api/admin/password_unknown_step_3', function () {

    $email = 'kron@naturwelt.at';
    $response = AdminControllerHelpers::passwordUnknownStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;
    $user->is_2fa = true;
    $user->email_2fa = "second@kronsoft.at";
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep2($email, $token_2fa);
    $user = User::where('email', $email)->first();
    $token_2fa_2 = $user->token_2fa_2;
    $user->is_2fa = false;
    $user->save();

    $response = AdminControllerHelpers::passwordUnknownStep3($email, $token_2fa, $token_2fa_2);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. 2-Faktoren-Authentifizierung ist nicht aktiviert.',
        ]);
});
