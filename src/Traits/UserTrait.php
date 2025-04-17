<?php

namespace Itstudioat\Spa\Traits;


/* =============================== 
ITStudioAT
=============================== */

trait UserTrait
{
    public function setToken2Fa($minutes): string
    {
        $this->token_2fa = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->token_2fa_expires_at = now()->addMinutes($minutes);
        $this->save();
        return $this->token_2fa;
    }
}
