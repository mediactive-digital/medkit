<?php

namespace MediactiveDigital\MedKit\Commands;

use InfyOm\Generator\Commands\BaseCommand as InfyOmBaseCommand;

use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Generators\MigrationGenerator;
use InfyOm\Generator\Generators\RepositoryGenerator;
use InfyOm\Generator\Generators\FactoryGenerator;
use InfyOm\Generator\Generators\SeederGenerator;
use InfyOm\Generator\Generators\Scaffold\RequestGenerator;
use InfyOm\Generator\Generators\Scaffold\ViewGenerator;
use InfyOm\Generator\Generators\Scaffold\RoutesGenerator;

use MediactiveDigital\MedKit\Generators\ModelGenerator;
use MediactiveDigital\MedKit\Generators\ControllerGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\MenuGenerator;

class BaseCommand extends InfyOmBaseCommand {

    /**
     * @var \MediactiveDigital\MedKit\Generators\ModelGenerator
     */
    public $modelGenerator;

    /**
     * @var \MediactiveDigital\MedKit\Generators\ControllerGenerator
     */
    public $controllerGenerator;

    public function generateCommonItems() {

        if (!$this->commandData->getOption('fromTable') and !$this->isSkip('migration')) {

            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }

        if (!$this->isSkip('model')) {

            $modelGenerator = new ModelGenerator($this->commandData);

            $this->generateModel();
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

    public function generateScaffoldItems() {

        if (!$this->isSkip('requests') and !$this->isSkip('scaffold_requests')) {

            $requestGenerator = new RequestGenerator($this->commandData);
            $requestGenerator->generate();
        }

        if (!$this->isSkip('controllers') and !$this->isSkip('scaffold_controller')) {

            $this->controllerGenerator = new ControllerGenerator($this->commandData);

            $this->generateForm();
            $this->generateController();
        }

        if (!$this->isSkip('views')) {

            $viewGenerator = new ViewGenerator($this->commandData);
            $viewGenerator->generate();
        }

        if (!$this->isSkip('routes') and !$this->isSkip('scaffold_routes')) {

            $routeGenerator = new RoutesGenerator($this->commandData);
            $routeGenerator->generate();
        }

        if (!$this->isSkip('menu') and $this->commandData->config->getAddOn('menu.enabled')) {

            $menuGenerator = new MenuGenerator($this->commandData);
            $menuGenerator->generate();
        }
    }
    
    public function performPostActions($runMigration = false) {

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

    /**
     * Generate model
     *
     * @return void
     */
    public function generateModel() {

        $path = $this->modelGenerator->getReflectionProperty('path');
        $fileName = $this->modelGenerator->getReflectionProperty('fileName');

        if (file_exists($path . $fileName) && !$this->confirmOverwrite($fileName)) {

            return;
        }

        $this->modelGenerator->generate();
    }

    /**
     * Generate form
     *
     * @return void
     */
    public function generateForm() {

        $path = $this->controllerGenerator->getReflectionProperty('formPath');
        $fileName = $this->controllerGenerator->getReflectionProperty('formFileName');

        if (file_exists($path . $fileName) && !$this->confirmOverwrite($fileName)) {

            return;
        }

        $this->controllerGenerator->generateForm();
    }

    /**
     * Generate controller
     *
     * @return void
     */
    public function generateController() {

        $path = $this->controllerGenerator->getReflectionProperty('path');
        $fileName = $this->controllerGenerator->getReflectionProperty('fileName');

        if (file_exists($path . $fileName) && !$this->confirmOverwrite($fileName)) {

            return;
        }

        $this->controllerGenerator->generate();
    }
}
