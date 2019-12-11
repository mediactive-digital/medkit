<?php

namespace App\Providers;
 
use App\Observers\ModelTrackObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
	 * 
	 * /!\ Ne pas supprimer les commentaires #
	 * 
     * @return void
     */
    public function boot()
    {
		
		# TracksHistory   
        \App\Models\Permission::observe(ModelTrackObserver::class);
        \App\Models\Role::observe(ModelTrackObserver::class);
        \App\Models\User::observe(ModelTrackObserver::class);
        # fin TracksHistory  
        
    }
}
