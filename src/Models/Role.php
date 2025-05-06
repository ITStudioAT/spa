<?php

// app/Models/Role.php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function shouldDelete(): bool
    {

        // Irgend ein User hat die Rolle noch zugeordnet
        if ($this->users()->exists()) {
            return false;
        }
        if ($this->name == 'super_admin') {
            return false;
        }

        $this->delete();

        return true;
    }
}
