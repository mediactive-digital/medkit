<?php

namespace MediactiveDigital\MedKit\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable {


    public static function boot() {

        parent::boot();
        static::observe(new AdminObserver);
    }

    /**
     * Hash password if needed.
     *
     * @param string $password
     * @return string
     */
    public function setPasswordAttribute($password) {

        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        return $this->notify(new ResetPassword($token));
    }

}