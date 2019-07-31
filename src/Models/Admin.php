<?php

namespace MediactiveDigital\MedKit\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Observers\AdminObserver;
use App\Notifications\ResetPassword;


class Admin extends Authenticatable {


    public static function boot() {
        parent::boot();
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