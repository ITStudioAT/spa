<?php

namespace Itstudioat\Spa\Traits;


/* =============================== 
ITStudioAT
=============================== */

trait UserTrait
{
    public function setToken2Fa($select = 1, $minutes): string
    {
        if ($select == 1) {
            $this->token_2fa = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $this->token_2fa_expires_at = now()->addMinutes($minutes);
            $this->save();
            return $this->token_2fa;
        } else {
            $this->token_2fa_2 = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $this->token_2fa_2_expires_at = now()->addMinutes($minutes);
            $this->save();
            return $this->token_2fa_2;
        }
    }

    public function checkToken2Fa($token_2fa): string
    {
        // Check if the token matches and is still valid
        if ($this->token_2fa == $token_2fa && now()->isBefore($this->token_2fa_expires_at)) {
            return true; // Token is valid and not expired
        }

        return false; // Token is invalid or expired
    }

    public function login()
    {
        auth()->login($this);
        $this->rememberLogin();
        request()->session()->regenerate();
    }

    private function rememberLogin()
    {
        $this->login_at = now();
        $this->login_ip = request()->ip();
        $this->save();
    }
}
