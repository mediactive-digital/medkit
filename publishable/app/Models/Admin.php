<?php

namespace App\Models;
use MediactiveDigital\MedKit\Models\Admin as MedKitModelAdmin;

use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\Back\ResetPassword;
use Wildside\Userstamps\Userstamps;
use App\Observers\AdminObserver;

class Admin extends MedKitModelAdmin
{

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
        'first_name', 
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
}