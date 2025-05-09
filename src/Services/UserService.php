<?php

namespace Itstudioat\Spa\Services;

use App\Models\Role;
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

    public function sendVerificationEmail($par_ids)
    // $par_ids ist ein Array von User_IDs oder eine einzelne User-ID
    // an alle diese User wird eine E-Mail-Verifikation gesendet.
    {

        $ids = is_array($par_ids) ? $par_ids : [$par_ids];
        foreach ($ids as $id) {
            $user = User::findOrFail($id);
            $user->sendVerificationEmail();
        }
    }

    public function setNewUserRoles($user_ids, $role_ids)
    {
        // set the role_names of each role
        foreach ($role_ids as &$role_id) {
            $role = Role::findOrFail($role_id['id']);
            $role_id['name'] = $role->name;
        }
        unset($role_id);

        // Run all users
        foreach ($user_ids as $id) {
            $user = User::findOrFail($id);

            foreach ($role_ids as $role_id) {
                if ($role_id['role_check'] == 1) {
                    // role should be assigned
                    $user->assignRole($role_id['name']);
                }

                if ($role_id['role_check'] == 2) {
                    // role should be removed
                    $user->removeRole($role_id['name']);
                }
            }
        }
    }
}
