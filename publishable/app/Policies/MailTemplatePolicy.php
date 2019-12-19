<?php
namespace App\Policies;

use App\Models\User;
use App\Models\MailTemplate; 
use Illuminate\Auth\Access\HandlesAuthorization;

class MailTemplatePolicy
{
    use HandlesAuthorization;
	
	/**  
     * Determine whether the user can view any mailTemplate.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user )
    { 
		 
		if ($user->can('mail-templates_view_own')) {
             return true;
        }
 
        if ( $user->can('mail-templates_view_all') ) { 
            return true;
        } 
    }
	
    /**
     * Determine whether the user can view the mailTemplate.
     *
     * @param  \App\User $user
     * @param  App\Models\MailTemplate $mailTemplate
     * @return mixed
     */
    public function view(User $user, MailTemplate $mailTemplate)
    {   
		
		 if ($user->can('mail-templates_view_own')) {
            return $user->id == $mailTemplate->created_by;
        }
		
        if ($user->can('mail-templates_view_all')) {
            return true;
        }
		
		/**
		 * Exemple Si il y a une methode published ?
		 * 
        if ($mailTemplate->published) {
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
        return $user->id == $mailTemplate->created_by; 
		 */
		
    }
    /**
     * Determine whether the user can create mailTemplate.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user )
    {	
		
        if ($user->can('mail-templates_create')) {
            return true;
        }
    }
	
	
    /**
     * Determine whether the user can update the mailTemplate.
     *
     * @param  \App\User $user
     * @param  App\Models\MailTemplate $mailTemplate
     * @return mixed
     */
    public function update(User $user, MailTemplate $mailTemplate)
    {  
		
		 if ($user->can('mail-templates_edit_own')) {
            return $user->id == $mailTemplate->created_by;
        }
		
        if ($user->can('mail-templates_edit_all')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can delete the mailTemplate.
     *
     * @param  \App\User $user
     * @param  App\Models\MailTemplate $mailTemplate
     * @return mixed
     */
    public function delete(User $user, MailTemplate $mailTemplate)
    {
		
		        if ($user->can('mail-templates_delete_own')) {
            return $user->id == $mailTemplate->created_by;
        } 
		
        if ($user->can('mail-templates_delete_any')) {
            return true;
        }
    }
	
    /**
     * Determine whether the user can restore the mailTemplate.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\MailTemplate mailTemplate
     * @return mixed
    public function restore(User $user, MailTemplate $mailTemplate)
    {
		
        //
		dd('restore');
    }
     */

    /**
     * Determine whether the user can permanently delete the mailTemplate.
     *
     * @param  \App\Models\User  $user
     * @param  App\Models\MailTemplate mailTemplate
     * @return mixed
    public function forceDelete(User $user, MailTemplate $mailTemplate)
    {
		
        //
		dd('forceDelete');
    }
     */
}