<?php
namespace App\Providers;

use MediactiveDigital\MedKit\Providers\AuthServiceProvider as MedKitAuthServiceProvider;

class AuthServiceProvider extends MedKitAuthServiceProvider
{

    /**
     * The policy mappings for the application.
     *
	 * /!\ Ne pas supprimer les commentaires #
	 * 
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
		# policiesGenerator   
		\App\Models\Permission::class => \App\Policies\PermissionPolicy::class,
		\App\Models\Role::class => \App\Policies\RolePolicy::class,
		\App\Models\User::class => \App\Policies\UserPolicy::class,
		\App\Models\MailTemplate::class => \App\Policies\MailTemplatePolicy::class,
        # fin policiesGenerator  
    ];
	
}