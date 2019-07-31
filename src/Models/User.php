<?php

namespace MediactiveDigital\MedKit\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


abstract class User extends Authenticatable {

    /**
     * Hash password if needed.
     *
     * @param string $password
     * @return string
     */
    public function setPasswordAttribute($password) {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

}