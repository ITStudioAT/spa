<?php

use App\Models\User;
use Tests\Controllers\AdminController\AdminControllerHelpers;

it('can password_unknown_step_4: /api/admin/password_unknown_step_4', function () {

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

    $password = "12345678";
    $password_repeat = "12345678";

    $response = AdminControllerHelpers::passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat);

    $response
        ->assertOk()
        ->assertJson([
            'step' => 'PASSWORD_UNKNOWN_FINISHED',
        ]);
});

it('cant password_unknown_step_4, wrong token_2fa: /api/admin/password_unknown_step_4', function () {

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

    $token_2fa = "123456";
    $password = "12345678";
    $password_repeat = "12345678";

    $response = AdminControllerHelpers::passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_4, token_2fa expired: /api/admin/password_unknown_step_4', function () {

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

    $user->token_2fa_expires_at = now()->subMinute();
    $user->save();
    $password = "12345678";
    $password_repeat = "12345678";

    $response = AdminControllerHelpers::passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_4, wrong token_2fa_2: /api/admin/password_unknown_step_4', function () {

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

    $token_2fa_2 = "123456";
    $password = "12345678";
    $password_repeat = "12345678";

    $response = AdminControllerHelpers::passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_4, token_2fa_2 expired: /api/admin/password_unknown_step_4', function () {

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

    $user->token_2fa_2_expires_at = now()->subMinute();
    $user->save();
    $password = "12345678";
    $password_repeat = "12345678";

    $response = AdminControllerHelpers::passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant password_unknown_step_4, password empty: /api/admin/password_unknown_step_4', function () {

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


    $password = "";
    $password_repeat = "";

    $response = AdminControllerHelpers::passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat);

    $response
        ->assertStatus(422);
});

it('cant password_unknown_step_4, password not password_repeat: /api/admin/password_unknown_step_4', function () {

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


    $password = "12345678";
    $password_repeat = "12345678XXX";

    $response = AdminControllerHelpers::passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Kennwort zurücksetzen funktioniert nicht. Kennwort und Wiederholung Kennwort sind nicht identisch',
        ]);
});
