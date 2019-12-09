<?php

namespace MediactiveDigital\MedKit\Commands\Scaffold;

// use Illuminate\Console\Command;
 
use MediactiveDigital\MedKit\Common\CommandData;

use MediactiveDigital\MedKit\Generators\ModelGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\MenuGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\RequestGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\ViewGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\TracksHistoryGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\PermissionGenerator; 
use MediactiveDigital\MedKit\Generators\ControllerGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\PolicyGenerator;

use InfyOm\Generator\Commands\RollbackGeneratorCommand as InfyOmRollbackGeneratorCommand;
use InfyOm\Generator\Generators\MigrationGenerator;
use InfyOm\Generator\Generators\RepositoryGenerator;
use InfyOm\Generator\Generators\Scaffold\RoutesGenerator;

// use Symfony\Component\Console\Input\InputArgument;
// use Symfony\Component\Console\Input\InputOption;

class RollbackGeneratorCommand extends InfyOmRollbackGeneratorCommand
{
    /**
     * The command Data.
     *
     * @var CommandData
     */
    public $commandData;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit:rollback';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback a full CRUD API and Scaffold for given model';

    /**
     * @var Composer
     */
    public $composer;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->composer = app()['composer'];
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if (!in_array($this->argument('type'), [
            CommandData::$COMMAND_TYPE_API,
            CommandData::$COMMAND_TYPE_SCAFFOLD,
            CommandData::$COMMAND_TYPE_API_SCAFFOLD,
            CommandData::$COMMAND_TYPE_VUEJS,
        ])) {
            $this->error('invalid rollback type');
        }

        $this->commandData = new CommandData($this, $this->argument('type'));
        $this->commandData->config->mName = $this->commandData->modelName = $this->argument('model');

        $this->commandData->config->init($this->commandData, ['tableName', 'prefix', 'plural', 'views']);

        $views = $this->commandData->getOption('views');
        if (!empty($views)) {
            $views = explode(',', $views);
            $viewGenerator = new ViewGenerator($this->commandData);
            $viewGenerator->rollback($views);

            $this->info('Generating autoload files');
            $this->composer->dumpOptimized();

            return;
        }

        $migrationGenerator = new MigrationGenerator($this->commandData);
        $migrationGenerator->rollback();

        $modelGenerator = new ModelGenerator($this->commandData);
        $modelGenerator->rollback();

        $repositoryGenerator = new RepositoryGenerator($this->commandData);
        $repositoryGenerator->rollback();
 
        $requestGenerator = new RequestGenerator($this->commandData);
        $requestGenerator->rollback();

        $controllerGenerator = new ControllerGenerator($this->commandData);
        $controllerGenerator->rollback();

        $viewGenerator = new ViewGenerator($this->commandData);
        $viewGenerator->rollback();

        $routeGenerator = new RoutesGenerator($this->commandData);
        $routeGenerator->rollback();
 
        if ($this->commandData->config->getAddOn('menu.enabled')) {
            $menuGenerator = new MenuGenerator($this->commandData);
            $menuGenerator->rollback();
        }

        if ( config('infyom.laravel_generator.add_on.tracks_history.enabled', true) ) {
            $trackerGenerator = new TracksHistoryGenerator($this->commandData);
            $trackerGenerator->rollback();
        }

        if ( config('infyom.laravel_generator.add_on.permissions.enabled', true) ) {
            $permissionsGenerator = new PermissionGenerator($this->commandData);
            $permissionsGenerator->rollback();
			
			if (!$this->isSkip('policies') and  config('infyom.laravel_generator.add_on.permissions.policies', true) ) {
				$policyGenerator = new PolicyGenerator($this->commandData);
				$policyGenerator->rollback();
			}
        }

        $this->info('Generating autoload files');
        $this->composer->dumpOptimized();
    }
 
}
