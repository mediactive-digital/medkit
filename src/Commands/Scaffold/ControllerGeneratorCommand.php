<?php

namespace MediactiveDigital\MedKit\Commands\Scaffold;

use InfyOm\Generator\Commands\Scaffold\ControllerGeneratorCommand as InfyOmControllerGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\ControllerGenerator;

class ControllerGeneratorCommand extends InfyOmControllerGeneratorCommand {

    use BaseCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit.scaffold:controller';

    /**
     * Create a new command instance.
     */
    public function __construct() {

        InfyOmBaseCommand::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle() {

        InfyOmBaseCommand::handle();

        $this->controllerGenerator = new ControllerGenerator($this->commandData);

        $this->generateForm();
        $this->generateHelper();
        $this->generateDataTable();
        $this->generateController();
        $this->performPostActions();
    }
}
