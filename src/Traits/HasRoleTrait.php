<?php

namespace Itstudioat\Spa\Traits;

trait HasRoleTrait
{

    public function userHasRole($par_roles)
    {
        if (!is_array($par_roles)) $roles[] = $par_roles;
        else $roles = $par_roles;

        if (!auth()->check()) $this->abort();
        if (!$user = auth()->user()) $this->abort();

        // Wenn super_admin in der Konfiguration gesetzt ist, füge ihn zu den erforderlichen Rollen hinzu
        if (!empty(config('spa.super_admin'))) {
            $roles[] = 'super_admin';
        }

        if (!$user->hasAnyRole($roles)) $this->abort();

        return $user;
    }

    private function abort()
    {
        abort(403, "Kein Zugriff!");
    }
}
