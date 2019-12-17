<?php

namespace MediactiveDigital\MedKit;

use Illuminate\Foundation\AliasLoader;

use Illuminate\Support\ServiceProvider;

use MediactiveDigital\MedKit\Providers\EventServiceProvider;
// use MediactiveDigital\MedKit\Providers\AppServiceProvider;
use MediactiveDigital\MedKit\Providers\TranslationServiceProvider;
use MediactiveDigital\MedKit\Providers\MacroServiceProvider;

use MediactiveDigital\MedKit\Commands\ClearDirectoryCommand;
use MediactiveDigital\MedKit\Commands\InstallCommand;
use MediactiveDigital\MedKit\Commands\RunMigrationCommand;
use MediactiveDigital\MedKit\Commands\CreateSuperAdminCommand;
use MediactiveDigital\MedKit\Commands\CleanupCommand;
use MediactiveDigital\MedKit\Commands\GenerateTranslationsCommand;
use MediactiveDigital\MedKit\Commands\GenerateJsTranslationsCommand;
use MediactiveDigital\MedKit\Commands\GenerateJsRoutesCommand;
use MediactiveDigital\MedKit\Commands\RollbackGeneratorCommand;
use MediactiveDigital\MedKit\Commands\Scaffold\ScaffoldGeneratorCommand;
use MediactiveDigital\MedKit\Commands\Scaffold\RequestsGeneratorCommand;
use MediactiveDigital\MedKit\Commands\Scaffold\ControllerGeneratorCommand;
use MediactiveDigital\MedKit\Commands\Scaffold\ViewsGeneratorCommand;
use MediactiveDigital\MedKit\Commands\Common\MigrationGeneratorCommand;
use MediactiveDigital\MedKit\Commands\Common\ModelGeneratorCommand;
use MediactiveDigital\MedKit\Commands\Common\RepositoryGeneratorCommand;

use MediactiveDigital\MedKit\Helpers\AssetHelper;

class MedKitServiceProvider extends ServiceProvider {

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {

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
    public function register() {

        $this->mergeConfigFrom(__DIR__ . '/../config/medkit.php', 'mediactive-digital.medkit');

        // Register the service the package provides.
        $this->app->singleton('medkit', function($app) {

            return new MedKit;
        });

        $this->app->singleton('assetConfJson', function() {

            return json_decode(file_get_contents(public_path('mdassets-autoload.json')), true);
        });

        $this->app->booting(function() {

            $loader = AliasLoader::getInstance();
            $loader->alias('MDAsset', AssetHelper::class);
        });

        $this->app->register(EventServiceProvider::class);
       // $this->app->register(AppServiceProvider::class);
        $this->app->register(TranslationServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);

        $this->registerCommands();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {

        return ['medkit'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole() {

        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/medkit.php' => config_path('mediactive-digital/medkit.php')
        ], 'medkit.config');
    }

    private function registerCommands() {
        
        $this->commands([
            InstallCommand::class,
            RunMigrationCommand::class,
            CreateSuperAdminCommand::class,
            ClearDirectoryCommand::class,
            CleanupCommand::class,
            ScaffoldGeneratorCommand::class,
            RequestsGeneratorCommand::class,
            ControllerGeneratorCommand::class,
            ViewsGeneratorCommand::class,
            MigrationGeneratorCommand::class,
            ModelGeneratorCommand::class,
            RepositoryGeneratorCommand::class,
            RollbackGeneratorCommand::class,
            GenerateTranslationsCommand::class,
            GenerateJsTranslationsCommand::class,
            GenerateJsRoutesCommand::class
        ]);
    }
}
