<?php

namespace MediactiveDigital\MedKit\Commands\Common;

use InfyOm\Generator\Commands\Common\RepositoryGeneratorCommand as InfyOmRepositoryGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\RepositoryGenerator;

class RepositoryGeneratorCommand extends InfyOmRepositoryGeneratorCommand {

    use BaseCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit:repository';

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

        $this->repositoryGenerator = new RepositoryGenerator($this->commandData);

        $this->generateRepository();
        $this->performPostActions();
    }
}
