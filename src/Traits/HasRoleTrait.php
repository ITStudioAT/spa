<?php

namespace Itstudioat\Spa\Traits;

trait HasRoleTrait
{
    public function userHasRole($par_roles)
    {
        if (! is_array($par_roles)) {
            $roles[] = $par_roles;
        } else {
            $roles = $par_roles;
        }

        if (! auth()->check()) {
            return false;
        }
        if (! $user = auth()->user()) {
            return false;
        }

        // Wenn super_admin in der Konfiguration gesetzt ist, füge ihn zu den erforderlichen Rollen hinzu
        $roles[] = 'super_admin';

        if (! $user->hasAnyRole($roles)) {
            return false;
        }

        return $user;
    }
}
