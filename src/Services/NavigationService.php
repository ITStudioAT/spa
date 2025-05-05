<?php

namespace Itstudioat\Spa\Services;

use App\Models\User;
use Itstudioat\Spa\Traits\HasRoleTrait;

class NavigationService
{
    use HasRoleTrait;

    public function menu(): array
    {
        $menu = [];
        if (! auth()->check()) {
            return [];
        }

        $user = User::findOrFail(auth()->user()->id);
        $user_name = substr($user->last_name . ' ' . $user->first_name, 0, 17);

        $menu[] = ['title' => 'Home', 'icon' => 'mdi-home', 'to' => '/admin'];

        // DASHBOARD
        if ($this->userHasRole(['admin'])) {
            $menu[] = ['title' => 'Dashboard', 'icon' => 'mdi-view-dashboard', 'to' => '/admin/dashboard'];
        }

        // BENUTZER
        if ($this->userHasRole(['admin'])) {
            $menu[] = ['title' => 'Benutzer', 'icon' => 'mdi-account-multiple', 'to' => '/admin/users'];
        }

        // PROFILE
        $menu[] = ['title' => $user_name, 'icon' => 'mdi-account', 'to' => '/admin/profile'];

        // ABMELDEN
        $menu[] = ['title' => 'Abmelden', 'icon' => 'mdi-power-cycle', 'click' => 'logout'];

        return $menu;
    }
}
