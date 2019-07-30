<?php

namespace MediactiveDigital\MedKit\Models;

use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Notifications\Back\ResetPassword;

use Wildside\Userstamps\Userstamps;

use App\Observers\AdminObserver;

use Hash;

class Admin extends Authenticatable {

    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use Userstamps;

    protected $table = 'users';
    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'firstname', 
        'email', 
        'login', 
        'password',
        'theme'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

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

    /**
     * Switch theme
     *
     * @param bool $theme
     * @return bool $switch
     */
    public function switchTheme(bool $theme) {

        $switch = false;

        if ($this->theme != $theme) {

            $this->update(['theme' => $theme]);
            $switch = true;
        }

        return $switch;
    }
}