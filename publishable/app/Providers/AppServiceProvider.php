<?php

namespace App\Providers;
 
use App\Observers\ModelTrackObserver;

use MediactiveDigital\MedKit\Services\Iseed;

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
        $this->app->bind('iseed', function() {
            return new Iseed;
        });
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
        \App\Models\MailTemplate::observe(ModelTrackObserver::class);
        # fin TracksHistory  
        
    }
}
