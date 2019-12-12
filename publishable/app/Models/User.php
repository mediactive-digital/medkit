<?php

namespace App\Models;

use MediactiveDigital\MedKit\Models\User as MedKitUser;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;  
use Eloquent as Model;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

use Wildside\Userstamps\Userstamps;

use Hash;

/**
 * Class User
 * @package App\Models
 * @version December 3, 2019, 11:49 pm UTC
 *
 * @property string name
 * @property string first_name
 * @property string email
 * @property string login
 * @property string password
 * @property boolean theme
 */
class User extends MedKitUser {
    
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use Userstamps;

    public $table = 'users';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const DELETED_BY = 'deleted_by';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_activity'
    ];

    protected $primaryKey = 'id';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token'
    ];
	
    public $fillable = [
        'name',
        'first_name',
        'email',
        'login',
        'password',
        'theme'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'first_name' => 'string',
        'email' => 'string',
        'login' => 'string',
        'password' => 'string',
        'theme' => 'boolean',
        'remember_token' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];
    
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
