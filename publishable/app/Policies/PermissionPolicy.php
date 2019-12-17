<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Permission; 
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;
	
	/**  
     * Determine whether the user can view any permission.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user )
    { 
		 
        if ( $user->can('permissions_view_all') ) { 
            return true;
        } 
    }
	
    /**
     * Determine whether the user can view the permission.
     *
     * @param  \App\User $user
     * @param  App\Models\Permission $permission
     * @return mixed
     */
    public function view(User $user, Permission $permission)
    {   
		 
        if ($user->can('permissions_view_all')) {
            return true;
        }
		
		/**
		 * Exemple Si il y a une methode published ?
		 * 
        if ($permission->published) {
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
        return $user->id == $permission->created_by; 
		 */
		
    }
    /**
     * Determine whether the user can create permission.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user )
    {	
		
        if ($user->can('permissions_create')) {
            return true;
        }
    }
	
	
    /**
     * Determine whether the user can update the permission.
     *
     * @param  \App\User $user
     * @param  App\Models\Permission $permission
     * @return mixed
     */
    public function update(User $user, Permission $permission)
    {  
		
		
		
        if ($user->can('permissions_edit_all')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can delete the permission.
     *
     * @param  \App\User $user
     * @param  App\Models\Permission $permission
     * @return mixed
     */
    public function delete(User $user, Permission $permission)
    {
		
		
		
        if ($user->can('permissions_delete_any')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can restore the permission.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\Permission permission
     * @return mixed
    public function restore(User $user, Permission $permission)
    {
		
        //
		dd('restore');
    }
     */

    /**
     * Determine whether the user can permanently delete the permission.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\Permission permission
     * @return mixed
    public function forceDelete(User $user, Permission $permission)
    {
		
        //
		dd('forceDelete');
    }
     */
}