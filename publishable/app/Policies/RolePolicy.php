<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Role; 
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
	
	/**  
     * Determine whether the user can view any role.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user )
    { 
		
        if ( $user->can('roles_view_all') ) { 
            return true;
        } 
    }
	
    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User $user
     * @param  App\Models\Role $role
     * @return mixed
     */
    public function view(User $user, Role $role)
    {   
		
        if ($user->can('roles_view_all')) {
            return true;
        }
		
		/**
		 * Exemple Si il y a une methode published ?
		 * 
        if ($role->published) {
            return true;
        }
        // visitors cannot view unpublished items
        if ($user === null) {
            return false;
        }
        // admin overrides published status
        if ($user->can('view unpublished permissions')) {
            return true;
        }
        // authors can view their own unpublished permissions
        return $user->id == $role->created_by; 
		 */
    }
		
    /**
     * Determine whether the user can create role.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user )
    {	
		
        if ($user->can('roles_create')) {
            return true;
        }
    }
	
	
    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User $user
     * @param  App\Models\Role $role
     * @return mixed
     */
    public function update(User $user, Role $role)
    {  
		
        if ($user->can('roles_edit_all')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User $user
     * @param  App\Models\Role $role
     * @return mixed
     */
    public function delete(User $user, Role $role)
    {

		if ($user->can('roles_delete_any')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\Role role
     * @return mixed
    public function restore(User $user, Role $role)
    {
		
        //
		dd('restore');
    }
     */

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\Role role
     * @return mixed
    public function forceDelete(User $user, Role $role)
    {
		
        //
		dd('forceDelete');
    }
     */
}