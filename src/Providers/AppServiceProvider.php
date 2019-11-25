<?php

namespace MediactiveDigital\MedKit\Providers;

use App\Models\Admin;
use App\Models\User;

use App\Observers\ModelTrackObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends MedKitAuthServiceProvider
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
     * @return void
     */
    public function boot()
    {
		// 
        // TRACKERS 
        // User::observe(ModelTrackObserver::class);
        Admin::observe(ModelTrackObserver::class);
		FeedMode::observe(ModelTrackObserver::class);
        
    }
}
