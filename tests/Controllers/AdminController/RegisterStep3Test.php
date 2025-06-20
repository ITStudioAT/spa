<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Tests\Controllers\AdminController\AdminControllerHelpers;



it('can register_step_3 - REGISTER_FINISHED: /api/admin/register_step_3', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);
    $response = AdminControllerHelpers::registerStep2($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $last_name = "Mustermann";
    $first_name = "Max";
    $password = "password12345";
    $password_repeat = $password;


    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_FIELDS',
            'email' => $email,
            'token_2fa' => $token_2fa,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'password' => $password,
            'password_repeat' => $password_repeat,

        ],
    ];

    Config::set('spa.registered_admin_must_be_confirmed', false);

    $response = $this->postJson('/api/admin/register_step_3', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'REGISTER_FINISHED',
        ]);
});


it('can register_step_3 - REGISTER_MUST_BE_CONFIRMED: /api/admin/register_step_3', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);
    $response = AdminControllerHelpers::registerStep2($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $last_name = "Mustermann";
    $first_name = "Max";
    $password = "password12345";
    $password_repeat = $password;


    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_FIELDS',
            'email' => $email,
            'token_2fa' => $token_2fa,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'password' => $password,
            'password_repeat' => $password_repeat,

        ],
    ];

    Config::set('spa.registered_admin_must_be_confirmed', true);

    $response = $this->postJson('/api/admin/register_step_3', $data)
        ->assertOk()
        ->assertJson([
            'step' => 'REGISTER_MUST_BE_CONFIRMED',
        ]);
});


it('cant register_step_3 - last_name empty: /api/admin/register_step_3', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);
    $response = AdminControllerHelpers::registerStep2($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $last_name = "";
    $first_name = "Max";
    $password = "password12345";
    $password_repeat = $password;


    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_FIELDS',
            'email' => $email,
            'token_2fa' => $token_2fa,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'password' => $password,
            'password_repeat' => $password_repeat,

        ],
    ];

    Config::set('spa.registered_admin_must_be_confirmed', false);

    $response = $this->postJson('/api/admin/register_step_3', $data)
        ->assertStatus(422);
});


it('cant register_step_3 - repeat_password not the samed: /api/admin/register_step_3', function () {

    $email = 'new@naturwelt.at';

    $response = AdminControllerHelpers::registerStep1($email);
    $response = AdminControllerHelpers::registerStep2($email);

    $user = User::where('email', $email)->first();
    $token_2fa = $user->token_2fa;

    $last_name = "Mustermann";
    $first_name = "Max";
    $password = "password12345";
    $password_repeat = $password . "xxx";


    $data = [
        'data' => [
            'step' => 'REGISTER_ENTER_FIELDS',
            'email' => $email,
            'token_2fa' => $token_2fa,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'password' => $password,
            'password_repeat' => $password_repeat,

        ],
    ];

    Config::set('spa.registered_admin_must_be_confirmed', false);

    $response = $this->postJson('/api/admin/register_step_3', $data)
        ->assertStatus(422);
});
