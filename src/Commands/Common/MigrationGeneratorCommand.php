<?php

namespace MediactiveDigital\MedKit\Commands\Common;

use InfyOm\Generator\Commands\Common\MigrationGeneratorCommand as InfyOmMigrationGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use MediactiveDigital\MedKit\Traits\BaseCommand;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Generators\MigrationGenerator;

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

        $this->migrationGenerator = new MigrationGenerator($this->commandData);

        $this->generateMigration();
        $this->performPostActionsWithMigration();
    }
}
