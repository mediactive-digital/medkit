<?php

namespace MediactiveDigital\MedKit\Commands\Common;

use InfyOm\Generator\Commands\Common\ModelGeneratorCommand as InfyOmModelGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\ModelGenerator;

class ModelGeneratorCommand extends InfyOmModelGeneratorCommand {

    use BaseCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit:model';

    /**
     * Create a new command instance.
     */
    public function __construct() {

        InfyOmBaseCommand::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_API);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle() {

        InfyOmBaseCommand::handle();

        $this->modelGenerator = new ModelGenerator($this->commandData);

        $this->generateModel();
        $this->performPostActions();
    }
}
