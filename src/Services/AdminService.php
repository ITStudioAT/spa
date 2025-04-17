<?php

namespace Itstudioat\Spa\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Itstudioat\Spa\Notifications\StandardEmail;


class AdminService
{

    public function checkUserLogin($data): User
    {
        if (!$user = User::where('email', $data['email'])->first()) abort(401, "Login funktioniert mit dieser E-Mail-Adresse nicht");
        if (!$user->is_active) abort(423, "Benutzer ist gesperrt");

        if ($data['step'] == 2) {
            if (!Hash::check($data['password'], $user->password)) abort(401, "Login funktioniert mit diesem Kennwort nicht");

            if (!$user->is_fa2) {
                // Login durchführen, sofern keine 2-Faktoren-Authentifizierung notwendig ist
                auth()->login($user);
                request()->session()->regenerate();
            }
        }

        if ($data['step'] == 3) {
            if (!Hash::check($data['password'], $user->password)) abort(401, "Login funktioniert mit diesem Kennwort nicht");

            if (!$user->checkToken2Fa($token_2fa))  abort(401, "Login funktioniert nicht. Die Zeit ist abgelaufen.");

            // Login durchführen
            auth()->login($user);
            request()->session()->regenerate();
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
