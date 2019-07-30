<?php

namespace MediactiveDigital\MedKit\Services\Auth;

use Illuminate\Auth\TokenGuard as TokenGuardContract;
use Illuminate\Support\Facades\Auth;

class TokenGuard extends TokenGuardContract {
    
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
