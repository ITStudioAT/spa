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
}
