<?php

namespace MediactiveDigital\MedKit\Commands\Scaffold;

use InfyOm\Generator\Commands\Scaffold\RequestsGeneratorCommand as InfyOmRequestsGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\Scaffold\RequestGenerator;

class RequestsGeneratorCommand extends InfyOmRequestsGeneratorCommand {

    use BaseCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit.scaffold:requests';

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

        $this->requestGenerator = new RequestGenerator($this->commandData);

        $this->generateRequest();
    }
}
