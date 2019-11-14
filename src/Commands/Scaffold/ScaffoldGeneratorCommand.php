<?php

namespace MediactiveDigital\MedKit\Commands\Scaffold;

use InfyOm\Generator\Commands\Scaffold\ScaffoldGeneratorCommand as InfyOmScaffoldGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Generators\MigrationGenerator;
use InfyOm\Generator\Generators\RepositoryGenerator;
use InfyOm\Generator\Generators\FactoryGenerator;
use InfyOm\Generator\Generators\SeederGenerator;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\ModelGenerator;

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
}
