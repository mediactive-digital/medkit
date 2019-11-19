<?php

namespace MediactiveDigital\MedKit\Commands\Scaffold;

use InfyOm\Generator\Commands\Scaffold\ScaffoldGeneratorCommand as InfyOmScaffoldGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Generators\MigrationGenerator;
use InfyOm\Generator\Generators\RepositoryGenerator;
use InfyOm\Generator\Generators\FactoryGenerator;
use InfyOm\Generator\Generators\SeederGenerator;
use InfyOm\Generator\Generators\Scaffold\RequestGenerator;
use InfyOm\Generator\Generators\Scaffold\ViewGenerator;
use InfyOm\Generator\Generators\Scaffold\RoutesGenerator;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\ModelGenerator;
use MediactiveDigital\MedKit\Generators\ControllerGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\MenuGenerator;

class ScaffoldGeneratorCommand extends InfyOmScaffoldGeneratorCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'medkit:scaffold';

    /**
     * Create a new command instance.
     */
    public function __construct() {

        BaseCommand::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);
    }

    public function generateCommonItems() {

        if (!$this->commandData->getOption('fromTable') and !$this->isSkip('migration')) {

            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }

        if (!$this->isSkip('model')) {

            $modelGenerator = new ModelGenerator($this->commandData);
            $modelGenerator->generate();
        }

        if (!$this->isSkip('repository') && $this->commandData->getOption('repositoryPattern')) {

            $repositoryGenerator = new RepositoryGenerator($this->commandData);
            $repositoryGenerator->generate();
        }

        if ($this->commandData->getOption('factory') || (!$this->isSkip('tests') and $this->commandData->getAddOn('tests'))) {

            $factoryGenerator = new FactoryGenerator($this->commandData);
            $factoryGenerator->generate();
        }

        if ($this->commandData->getOption('seeder')) {

            $seederGenerator = new SeederGenerator($this->commandData);
            $seederGenerator->generate();
            $seederGenerator->updateMainSeeder();
        }
    }

    public function generateScaffoldItems() {

        if (!$this->isSkip('requests') and !$this->isSkip('scaffold_requests')) {

            $requestGenerator = new RequestGenerator($this->commandData);
            $requestGenerator->generate();
        }

        if (!$this->isSkip('controllers') and !$this->isSkip('scaffold_controller')) {

            $controllerGenerator = new ControllerGenerator($this->commandData);
            $controllerGenerator->generate();
        }

        if (!$this->isSkip('views')) {

            $viewGenerator = new ViewGenerator($this->commandData);
            $viewGenerator->generate();
        }

        if (!$this->isSkip('routes') and !$this->isSkip('scaffold_routes')) {

            $routeGenerator = new RoutesGenerator($this->commandData);
            $routeGenerator->generate();
        }

        if (!$this->isSkip('menu') and $this->commandData->config->getAddOn('menu.enabled')) {

            $menuGenerator = new MenuGenerator($this->commandData);
            $menuGenerator->generate();
        }
    }
}
