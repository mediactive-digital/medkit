<?php

namespace MediactiveDigital\MedKit\Commands\Scaffold;

use InfyOm\Generator\Commands\Scaffold\ControllerGeneratorCommand as InfyOmControllerGeneratorCommand;
use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Generators\ControllerGenerator;
use MediactiveDigital\MedKit\Common\CommandData;

class ControllerGeneratorCommand extends InfyOmControllerGeneratorCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'medkit.scaffold:controller';

    /**
     * Create a new command instance.
     */
    public function __construct() {

        BaseCommand::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);
    }

     /**
     * Execute the command.
     *
     * @return void
     */
    public function handle() {

        BaseCommand::handle();

        $controllerGenerator = new ControllerGenerator($this->commandData);
        $controllerGenerator->generateForm();
        $controllerGenerator->generate();

        $this->performPostActions();
    }

    public function performPostActions($runMigration = false) {

        if ($this->commandData->getOption('save')) {

            $this->saveSchemaFile();
        }

        if ($runMigration) {

            if ($this->commandData->getOption('forceMigrate')) {

                $this->runMigration();
            } 
            elseif (!$this->commandData->getOption('fromTable') and !$this->isSkip('migration')) {

                $requestFromConsole = php_sapi_name() == 'cli' ? true : false;

                if ($this->commandData->getOption('jsonFromGUI') && $requestFromConsole) {

                    $this->runMigration();
                }
                elseif ($requestFromConsole && $this->confirm("\nDo you want to migrate database? [y|N]", false)) {

                    $this->runMigration();
                }
            }
        }

        if (!$this->isSkip('dump-autoload')) {

            $this->info('Generating autoload files');
            $this->composer->dumpOptimized();
        }
    }

    private function saveSchemaFile() {

        $fileFields = [];

        foreach ($this->commandData->fields as $field) {

            $fileFields[] = [
                'name' => $field->name,
                'dbType' => $field->dbInput,
                'htmlType' => $field->htmlInput ?: $field->htmlType,
                'validations' => $field->validations,
                'searchable' => $field->isSearchable,
                'fillable' => $field->isFillable,
                'primary' => $field->isPrimary,
                'inForm' => $field->inForm,
                'inIndex' => $field->inIndex,
                'inView' => $field->inView,
            ];
        }

        foreach ($this->commandData->relations as $relation) {

            $fileFields[] = [
                'type' => 'relation',
                'relation' => $relation->type . ',' . implode(',', $relation->inputs),
            ];
        }

        $path = config('infyom.laravel_generator.path.schema_files', resource_path('model_schemas/'));

        $fileName = $this->commandData->modelName . '.json';

        if (file_exists($path . $fileName) && !$this->confirmOverwrite($fileName)) {

            return;
        }

        FileUtil::createFile($path, $fileName, json_encode($fileFields, JSON_PRETTY_PRINT));
        $this->commandData->commandComment("\nSchema File saved: ");
        $this->commandData->commandInfo($fileName);
    }
}
