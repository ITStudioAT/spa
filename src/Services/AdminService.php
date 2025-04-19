<?php

namespace Itstudioat\Spa\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Itstudioat\Spa\Notifications\StandardEmail;


class AdminService
{

    public function checkPasswordUnknown($data): User
    {
        if (!$user = User::where('email', $data['email'])->first()) abort(401, "Kennwort zurücksetzen funktioniert mit dieser E-Mail-Adresse nicht");
        if (!$user->is_active) abort(423, "Benutzer ist gesperrt");

        if ($data['step'] == 'PASSWORD_UNKNOWN_ENTER_TOKEN') {
            if (!$user->checkToken2Fa($data['token_2fa']))  abort(401, "Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.");
        }

        if ($data['step'] == 'PASSWORD_UNKNOWN_ENTER_TOKEN_2') {
            if (!$user->checkToken2Fa($data['token_2fa']))  abort(401, "Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.");
            if (!$user->checkToken2Fa_2($data['token_2fa_2']))  abort(401, "Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.");
        }

        if ($data['step'] == 'PASSWORD_UNKNOWN_ENTER_PASSWORD') {
            if (!$user->checkToken2Fa($data['token_2fa']))  abort(401, "Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.");
            if (!$user->is_fa2 && !$user->checkToken2Fa_2($data['token_2fa_2']))  abort(401, "Kennwort zurücksetzen funktioniert nicht. Code falsch oder Zeit abgelaufen.");

            if ($data['password'] != $data['password_repeat']) abort(401, "Kennwort und Widerholung Kennwort sind nicht identisch");
        }

        return $user;
    }

    public function checkUserLogin($data): User
    {
        if (!$user = User::where('email', $data['email'])->first()) abort(401, "Login funktioniert mit dieser E-Mail-Adresse nicht");
        if (!$user->is_active) abort(423, "Benutzer ist gesperrt");

        if ($data['step'] == 'LOGIN_ENTER_PASSWORD') {
            if (!Hash::check($data['password'], $user->password)) abort(401, "Login funktioniert mit diesem Kennwort nicht");
        }

        if ($data['step'] == 'LOGIN_ENTER_TOKEN') {
            if (!Hash::check($data['password'], $user->password)) abort(401, "Login funktioniert mit diesem Kennwort nicht");
            if (!$user->checkToken2Fa($data['token_2fa']))  abort(401, "Login funktioniert nicht. Code falsch oder Zeit abgelaufen.");
        }

        return $user;
    }

    public function continueLoginFor2FaUser($user)
    {

        $token_2fa = $user->setToken2Fa(1, config('spa.token_expire_time'));

        $data = [
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'subject' => 'Code für Login',
            'markdown' => 'spa::mails.admin.sendCode',
            'token_2fa' => $token_2fa,
            'token-expire-time' => config('spa.token_expire_time'),
        ];

        Notification::route('mail', $user->email)->notify(new StandardEmail($data));
    }


    public function sendPasswordResetToken($select = 1, $user, $email)
    {
        $token_2fa = $user->setToken2Fa($select, config('spa.token_expire_time'));

        $data = [
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'subject' => 'Code zum Zurücksetzen des Kennwortes',
            'markdown' => 'spa::mails.admin.sendCode',
            'token_2fa' => $token_2fa,
            'token-expire-time' => config('spa.token_expire_time'),
        ];

        Notification::route('mail', $email)->notify(new StandardEmail($data));
    }
}
