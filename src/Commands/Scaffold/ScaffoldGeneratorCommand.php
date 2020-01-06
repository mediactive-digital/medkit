<?php

namespace MediactiveDigital\MedKit\Commands\Scaffold;

use InfyOm\Generator\Commands\Scaffold\ScaffoldGeneratorCommand as InfyOmScaffoldGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;

class ScaffoldGeneratorCommand extends InfyOmScaffoldGeneratorCommand {

    use BaseCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit:scaffold';

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

        if ($this->checkIsThereAnyDataToGenerate()) {

            $this->generateCommonItems();
            $this->generateScaffoldItems();
            $this->performPostActionsWithMigration();
        } 
        else {

            $this->commandData->commandInfo('There are not enough input fields for scaffold generation.');
        }
    }
}
