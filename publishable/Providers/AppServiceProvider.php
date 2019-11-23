<?php

namespace App\Providers;

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
     * @return void
     */
    public function boot()
    {
		// 
        // TRACKERS 
        // User::observe(ModelTrackObserver::class);
        Admin::observe(ModelTrackObserver::class);
    }
}
