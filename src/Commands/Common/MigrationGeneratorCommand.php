<?php

namespace MediactiveDigital\MedKit\Commands\Common;

use InfyOm\Generator\Commands\Common\MigrationGeneratorCommand as InfyOmMigrationGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;
use InfyOm\Generator\Generators\MigrationGenerator;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;

class MigrationGeneratorCommand extends InfyOmMigrationGeneratorCommand {

    use BaseCommand;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'medkit:migration';

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

        $migrationGenerator = new MigrationGenerator($this->commandData);

        $migrationGenerator->generate();
        $this->performPostActionsWithMigration();
    }
}
