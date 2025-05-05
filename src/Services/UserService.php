<?php

namespace Itstudioat\Spa\Services;

use App\Models\User;

class UserService
{
    public function allUsersInfos(): array
    {
        $data = [];
        $users_count = User::query()->count();
        $users_is_active_count = User::where('is_active', 1)->count();
        $users_is_2fa_count = User::where('is_2fa', 1)->count();
        $users_is_confirmed_count = User::whereNotNull('confirmed_at')->count();
        $users_is_email_verified_count = User::whereNotNull('email_verified_at')->count();

        $data = [
            ['title' => 'Benutzer gesamt', 'content' => $users_count],
            ['title' => 'Aktive Benutzer', 'content' => $users_is_active_count],
            ['title' => 'Benutzer mit 2-FA-Authentifizierung', 'content' => $users_is_2fa_count],
            ['title' => 'Benutzer mit bestätigter E-Mail', 'content' => $users_is_email_verified_count],
            ['title' => 'Bestätigte Benutzer', 'content' => $users_is_confirmed_count],
        ];

        return $data;
    }
}
