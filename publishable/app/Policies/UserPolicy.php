<?php
namespace App\Policies;
 
use App\Models\User; 
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
	
	/**  
     * Determine whether the user can view any user.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user )
    { 
		 
		
if ($user->can('users_view_own')) {
             return true;
        }
 
        if ( $user->can('users_view_all') ) { 
            return true;
        } 
    }
	
    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\User $user
     * @param  App\Models\User $user
     * @return mixed
     */
    public function view(User $u, User $user )
    {    
		
 if ($u->can('users_view_own')) {
            return $u->id == $user->created_by;
        }
		
        if ($u->can('users_view_all')) {
            return true;
        }
		
		/**
		 * Exemple Si il y a une methode published ?
		 * 
        if ($user->published) {
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
        return $user->id == $user->created_by; 
		 */
		
    }
    /**
     * Determine whether the user can create user.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user )
    {	
		
        if ($user->can('users_create')) {
            return true;
        }
    }
	
	
    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\User $user
     * @param  App\Models\User $user
     * @return mixed
     */
    public function update(User $u, User $user )
    {  
		
		
 if ($u->can('users_edit_own')) {
            return $u->id == $user->created_by;
        }
		
        if ($u->can('users_edit_all')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User $user
     * @param  App\Models\User $user
     * @return mixed
     */
    public function delete(User $u, User $user )
    {
		
		 

        if ($u->can('users_delete_own')) {
            return $u->id == $user->created_by;
        } 

		
        if ($u->can('users_delete_any')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can restore the user.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\User user
     * @return mixed
    public function restore(User $user, User $user)
    {
		
        //
		dd('restore');
    }
     */

    /**
     * Determine whether the user can permanently delete the user.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\User user
     * @return mixed
    public function forceDelete(User $user, User $user)
    {
		
        //
		dd('forceDelete');
    }
     */
}