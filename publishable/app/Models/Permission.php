<?php

namespace App\Models;

use Eloquent as Model;

use Carbon\Carbon;



/**
 * Class Permission
 * @package App\Models
 * @version December 3, 2019, 11:24 pm UTC
 *
 * @property \App\Models\ModelHasPermission modelHasPermission
 * @property \Illuminate\Database\Eloquent\Collection roles
 * @property string name
 * @property string guard_name
 */
class Permission extends Model {
    



    public $table = 'permissions';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $primaryKey = 'id';

    public $fillable = [
        'name',
        'guard_name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'guard_name' => 'string'
    ];
    
    
/**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function modelHasPermission() {
    
        return $this->hasOne(\App\Models\ModelHasPermission::class);
    }

    
/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function roles() {
    
        return $this->belongsToMany(\App\Models\Role::class, 'role_has_permissions');
    }
}
