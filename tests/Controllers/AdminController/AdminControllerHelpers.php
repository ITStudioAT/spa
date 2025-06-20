<?php

// tests/Helpers/RegisterHelpers.php
namespace Tests\Controllers\AdminController;

use App\Models\User;

class AdminControllerHelpers
{
    public static function registerStep1($email)
    {
        $data = [
            'data' => [
                'step' => 'REGISTER_ENTER_EMAIL',
                'email' => $email,
            ],
        ];

        return test()->postJson('/api/admin/register_step_1', $data);
    }

    public static function registerStep2($email)
    {
        $user = User::where('email', $email)->first();
        $token_2fa = $user->token_2fa;

        $data = [
            'data' => [
                'step' => 'REGISTER_ENTER_TOKEN',
                'email' => $email,
                'token_2fa' => $token_2fa,
            ],
        ];

        return test()->postJson('/api/admin/register_step_2', $data);
    }

    public static function passwordUnknownStep1($email)
    {
        $data = [
            'data' => [
                'step' => 'PASSWORD_UNKNOWN_ENTER_EMAIL',
                'email' => $email,
            ],
        ];

        return test()->postJson('/api/admin/password_unknown_step_1', $data);
    }

    public static function passwordUnknownStep2($email, $token_2fa)
    {
        $data = [
            'data' => [
                'step' => 'PASSWORD_UNKNOWN_ENTER_TOKEN',
                'email' => $email,
                'token_2fa' => $token_2fa,
            ],
        ];

        return test()->postJson('/api/admin/password_unknown_step_2', $data);
    }

    public static function passwordUnknownStep3($email, $token_2fa, $token_2fa_2)
    {
        $data = [
            'data' => [
                'step' => 'PASSWORD_UNKNOWN_ENTER_TOKEN_2',
                'email' => $email,
                'token_2fa' => $token_2fa,
                'token_2fa_2' => $token_2fa_2,
            ],
        ];

        return test()->postJson('/api/admin/password_unknown_step_3', $data);
    }

    public static function passwordUnknownStep4($email, $token_2fa, $token_2fa_2, $password, $password_repeat)
    {
        $data = [
            'data' => [
                'step' => 'PASSWORD_UNKNOWN_ENTER_PASSWORD',
                'email' => $email,
                'token_2fa' => $token_2fa,
                'token_2fa_2' => $token_2fa_2,
                'password' => $password,
                'password_repeat' => $password_repeat,
            ],
        ];

        return test()->postJson('/api/admin/password_unknown_step_4', $data);
    }
}
