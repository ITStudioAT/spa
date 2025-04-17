<?php

namespace Itstudioat\Spa\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Itstudioat\Spa\Notifications\StandardEmail;


class AdminService
{

    public function checkUserLogin($email, $password = null, $token_2fa = null): User
    {
        if (!$user = User::where('email', $email)->first()) abort(401, "Login funktioniert mit dieser E-Mail-Adresse nicht");
        if (!$user->is_active) abort(423, "Benutzer ist gesperrt");


        if ($password) {
            if (!Hash::check($password, $user->password)) abort(401, "Login funktioniert mit diesem Kennwort nicht");

            // Login durchführen, sofern keine 2-Faktoren-Authentifizierung notwendig ist
            if (!$user->is_fa2) {
                auth()->login($user);
                request()->session()->regenerate();
            }
        }

        return $user;
    }

    public function continueLoginFor2FaUser($user)
    {
        $token_2fa = $user->setToken2Fa(config('spa.token-expire-time'));

        $data = [
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'subject' => 'Code für Login',
            'markdown' => 'spa::mails.admin.login2FaCode',
            'token_2fa' => $token_2fa,
            'token-expire-time' => config('spa.token-expire-time'),
        ];

        Notification::route('mail', $user->email)->notify(new StandardEmail($data));
    }
}
