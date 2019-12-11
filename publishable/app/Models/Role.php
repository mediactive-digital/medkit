<?php

namespace App\Models;

use Eloquent as Model;

use Carbon\Carbon;

use MediactiveDigital\MedKit\Models\Role as MedKitRole;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;


/**
 * Class Role
 * @package App\Models
 * @version December 3, 2019, 11:48 pm UTC
 *
 * @property \App\Models\ModelHasRole modelHasRole
 * @property \Illuminate\Database\Eloquent\Collection permissions
 * @property string name
 * @property string guard_name
 */
class Role extends MedKitRole {
     
    use Cachable;

    const SUPER_ADMIN_ID = 1;
    const ADMIN_ID = 2;

    const SUPER_ADMIN = 'Super admin';
    const ADMIN = 'Admin';
    const ROLES_ADMIN = [ self::SUPER_ADMIN, self::ADMIN ]; //roles étant considérés comme admin

    protected $cacheCooldownSeconds = 86400; // un jour


    public $table = 'roles';
    
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
    public function modelHasRole() {
    
        return $this->hasOne(\App\Models\ModelHasRole::class);
    }

    
/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function permissions() {
    
        return $this->belongsToMany(\App\Models\Permission::class, 'role_has_permissions');
    }
}
