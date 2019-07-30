<?php

namespace MediactiveDigital\MedKit\Services\Auth;

use Illuminate\Auth\SessionGuard as SessionGuardContract;
use Illuminate\Support\Facades\Auth;

class SessionGuard extends SessionGuardContract {
    
    public function getGuard(array $guards = []) {

        $guards = empty($guards) ? array_keys(config('auth.guards')) : $guards;

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {

                return $guard;
            }
        }

        return null;
    }
}
