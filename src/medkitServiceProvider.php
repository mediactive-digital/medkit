<?php

namespace MediactiveDigital\MedKit;

use Illuminate\Support\ServiceProvider;

class MedKitServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mediactivedigital');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'mediactivedigital');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/medkit.php', 'medkit');

        // Register the service the package provides.
        $this->app->singleton('medkit', function ($app) {
            return new MedKit;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['medkit'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/medkit.php' => config_path('medkit.php'),
        ], 'medkit.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/mediactivedigital'),
        ], 'medkit.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/mediactivedigital'),
        ], 'medkit.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/mediactivedigital'),
        ], 'medkit.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
