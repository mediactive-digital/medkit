<?php

namespace MediactiveDigital\MedKit\Commands\Common;

use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\SeederGenerator;

class SeederGeneratorCommand extends InfyOmBaseCommand {

    use BaseCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit:seeder';

    /**
     * Create a new command instance.
     */
    public function __construct() {

        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_API);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle() {

        parent::handle();

        $this->seederGenerator = new SeederGenerator($this->commandData);

        $this->generateSeeder();
        $this->performPostActions();
    }
}
