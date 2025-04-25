<?php

namespace Itstudioat\Spa\Services;

use app\Models\User;
use Spatie\Permission\Models\Role;
use Itstudioat\Spa\Enums\RouteResult;



class NavigationService
{

    public function menu(): array
    {
        $menu = [];
        if (!auth()->check()) return [];

        $user  = auth()->user();
        $user_name = substr($user->last_name . ' ' . $user->first_name, 0, 17);

        $menu[] = ['title' => 'Home', 'icon' => 'mdi-home', 'to' => '/admin'];
        $menu[] = ['title' => 'Dashboard', 'icon' => 'mdi-view-dashboard', 'to' => '/admin/dashboard'];
        $menu[] = ['title' => 'Benutzer', 'icon' => 'mdi-account-multiple', 'to' => '/admin/users'];
        $menu[] = ['title' => 'Einstellungen', 'icon' => 'mdi-cog', 'to' => '/admin/settings'];
        $menu[] = ['title' => $user_name, 'icon' => 'mdi-account', 'to' => '/admin/profile'];
        $menu[] = ['title' => 'Abmelden', 'icon' => 'mdi-power-cycle', 'click' => 'logout'];




        return $menu;
    }
}
