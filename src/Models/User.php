<?php

namespace MediactiveDigital\MedKit\Models;

use Soved\Laravel\Gdpr\Portable;
use Soved\Laravel\Gdpr\Contracts\Portable as PortableContract;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Soved\Laravel\Gdpr\Retentionable;

use MediactiveDigital\MedKit\Notifications\ResetPassword;

use Hash;

abstract class User extends Authenticatable implements PortableContract {

    use Retentionable, Portable, Notifiable;


    /**
     * The attributes that should be hidden for the downloadable data.
     *
     * @var array
     */
    protected $gdprHidden = ['password'];

    /**
     * The relations to include in the downloadable data.
     *
     * @var array
     */
    // protected $gdprWith = ['posts'];

    /**
     * The attributes that should be visible in the downloadable data.
     *
     * @var array
     */
    // protected $gdprVisible = ['name', 'email'];

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
     * Get the GDPR compliant data portability array for the model.
     *
     * @return array
     */
    public function toPortableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
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
