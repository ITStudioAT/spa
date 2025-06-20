<?php


use App\Models\User;
use Tests\Controllers\AdminController\AdminControllerHelpers;




it('can register_step_2: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_TOKEN',
            'email' => $email,
            'token_2fa' => $token_2fa,
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_2', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'REGISTER_ENTER_FIELDS',
        ]);
});

it('cant register_step_2, wrong step: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_TOKEN_XXX',
            'email' => $email,
            'token_2fa' => $token_2fa,
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_2', $data)
        ->assertStatus(422);
});

it('cant register_step_2, wrong email: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_TOKEN',
            'email' => 'wrong@naturwelt.at',
            'token_2fa' => $token_2fa,
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Registrieren funktioniert mit dieser E-Mail-Adresse nicht.',
        ]);
});

it('cant register_step_2, correct email, but email is already registered: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_TOKEN',
            'email' => 'kron@naturwelt.at',
            'token_2fa' => $token_2fa,
        ],
    ];

    $response = $this->postJson('/api/admin/register_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Registrieren funktioniert mit dieser E-Mail-Adresse nicht.',
        ]);
});

it('cant register_step_2, fa2_time overflow: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $user->token_2fa_expires_at = now()->subMinute();
    $user->save();

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_TOKEN',
            'email' => $email,
            'token_2fa' => $token_2fa,

        ],
    ];

    $response = $this->postJson('/api/admin/register_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Registrieren funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});

it('cant register_step_2, fa2_token wrong: /api/admin/register_step_1', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_TOKEN',
            'email' => $email,
            'token_2fa' => '123456',

        ],
    ];

    $response = $this->postJson('/api/admin/register_step_2', $data)
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Registrieren funktioniert nicht. Code falsch oder Zeit abgelaufen.',
        ]);
});
