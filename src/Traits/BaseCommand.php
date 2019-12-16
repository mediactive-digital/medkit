<?php

namespace MediactiveDigital\MedKit\Traits;

use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Generators\MigrationGenerator;
use InfyOm\Generator\Generators\Scaffold\RoutesGenerator;

use MediactiveDigital\MedKit\Generators\ModelGenerator;
use MediactiveDigital\MedKit\Generators\RepositoryGenerator;
use MediactiveDigital\MedKit\Generators\FactoryGenerator;
use MediactiveDigital\MedKit\Generators\SeederGenerator;
use MediactiveDigital\MedKit\Generators\ControllerGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\RequestGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\MenuGenerator;
use MediactiveDigital\MedKit\Generators\Scaffold\ViewGenerator; 
use MediactiveDigital\MedKit\Generators\Scaffold\TracksHistoryGenerator; 
use MediactiveDigital\MedKit\Generators\Scaffold\PermissionGenerator; 
use MediactiveDigital\MedKit\Generators\Scaffold\PolicyGenerator; 

trait BaseCommand { 

    /**
     * @var \MediactiveDigital\MedKit\Generators\ModelGenerator
     */
    public $modelGenerator;

    /**
     * @var MediactiveDigital\MedKit\Generators\RepositoryGenerator
     */
    public $repositoryGenerator;

    /**
     * @var MediactiveDigital\MedKit\Generators\FactoryGenerator
     */
    public $factoryGenerator;

    /**
     * @var MediactiveDigital\MedKit\Generators\SeederGenerator
     */
    public $seederGenerator;

    /**
     * @var \MediactiveDigital\MedKit\Generators\Scaffold\requestGenerator
     */
    public $requestGenerator;

    /**
     * @var \MediactiveDigital\MedKit\Generators\ControllerGenerator
     */
    public $controllerGenerator;
	
    /**
     * @var \MediactiveDigital\MedKit\Generators\ViewGenerator
     */
    public $viewGenerator;

    /**
     * @var \MediactiveDigital\MedKit\Generators\PolicyGenerator
     */
    public $policyGenerator;
	
    public function generateCommonItems() {

        if (!$this->commandData->getOption('fromTable') and !$this->isSkip('migration')) {

            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }

        if (!$this->isSkip('model')) {

            $this->modelGenerator = new ModelGenerator($this->commandData);

            $this->generateModel();
        }

        if (!$this->isSkip('repository') && $this->commandData->getOption('repositoryPattern')) {

            $this->repositoryGenerator = new RepositoryGenerator($this->commandData);

            $this->generateRepository();
        }

        if ($this->commandData->getOption('factory') || (!$this->isSkip('tests') and $this->commandData->getAddOn('tests'))) {

            $this->factoryGenerator = new FactoryGenerator($this->commandData);

            $this->generateFactory();
        }

        if ($this->commandData->getOption('seeder')) {

            $this->seederGenerator = new SeederGenerator($this->commandData);

            $this->generateSeeder();
            $this->seederGenerator->updateMainSeeder();
        }
		
    }

    public function generateScaffoldItems() {

        if (!$this->isSkip('requests') and !$this->isSkip('scaffold_requests')) {

            $this->requestGenerator = new RequestGenerator($this->commandData);
            
            $this->generateRequest();
        }

        if (!$this->isSkip('controllers') and !$this->isSkip('scaffold_controller')) {

            $this->controllerGenerator = new ControllerGenerator($this->commandData);

            $this->generateForm();
            $this->generateDataTable();
            $this->generateController();
        }

        if (!$this->isSkip('views')) {

            $this->viewGenerator = new ViewGenerator($this->commandData); 
            $this->generateView();
        }

        if (!$this->isSkip('routes') and !$this->isSkip('scaffold_routes')) {

            $routeGenerator = new RoutesGenerator($this->commandData);
            $routeGenerator->generate();
        }

        if (!$this->isSkip('menu') and $this->commandData->config->getAddOn('menu.enabled')) {

            $menuGenerator = new MenuGenerator($this->commandData);
            $menuGenerator->generate();
        }
		 
		if (!$this->isSkip('tracks_history') and  config('infyom.laravel_generator.add_on.tracks_history.enabled', true) ) {
		
            $trackerGenerator = new TracksHistoryGenerator($this->commandData);
            $trackerGenerator->generate(); 
		}
        
		if (!$this->isSkip('permissions') and  config('infyom.laravel_generator.add_on.permissions.enabled', true) ) {
		
            $permissionsGenerator = new PermissionGenerator($this->commandData);
            $permissionsGenerator->generate(); 
			
			
			if (!$this->isSkip('policies') and  config('infyom.laravel_generator.add_on.permissions.policies', true) ) {

				$this->policyGenerator = new PolicyGenerator($this->commandData);

				$this->generatePolicy(); 
				$this->policyGenerator->generateProvider(); 
			}
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
                'htmlType' => $field->htmlType,
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

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Schema ' . $fileName)) {

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

        $path = $this->modelGenerator->getReflectionProperty('path', true);
        $fileName = $this->modelGenerator->getReflectionProperty('fileName', true);

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Model ' . $fileName)) {

            return;
        }

        $this->modelGenerator->generate();
    }

    /**
     * Generate repository
     *
     * @return void
     */
    public function generateRepository() {

        $path = $this->repositoryGenerator->getReflectionProperty('path');
        $fileName = $this->repositoryGenerator->getReflectionProperty('fileName');

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Repository ' . $fileName)) {

            return;
        }

        $this->repositoryGenerator->generate();
    }

    /**
     * Generate factory
     *
     * @return void
     */
    public function generateFactory() {

        $path = $this->factoryGenerator->getReflectionProperty('path');
        $fileName = $this->factoryGenerator->getReflectionProperty('fileName');

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Factory ' . $fileName)) {

            return;
        }

        $this->factoryGenerator->generate();
    }

    /**
     * Generate seeder
     *
     * @return void
     */
    public function generateSeeder() {

        $path = $this->seederGenerator->getReflectionProperty('path');
        $fileName = $this->seederGenerator->getReflectionProperty('fileName');

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Seeder ' . $fileName)) {

            return;
        }

        $this->seederGenerator->generate();
    }

    /**
     * Generate request
     *
     * @return void
     */
    public function generateRequest() {

        $path = $this->requestGenerator->getReflectionProperty('path', true);
        $fileName = $this->requestGenerator->getReflectionProperty('fileName', true);

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Request ' . $fileName)) {

            return;
        }

        $this->requestGenerator->callReflectionMethod('generateRequest');
    }

    /**
     * Generate form
     *
     * @return void
     */
    public function generateForm() {

        $path = $this->controllerGenerator->getReflectionProperty('formPath', true);
        $fileName = $this->controllerGenerator->getReflectionProperty('formFileName', true);

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Form ' . $fileName)) {

            return;
        }

        $this->controllerGenerator->generateForm();
    }

    /**
     * Generate datatable
     *
     * @return void
     */
    public function generateDataTable() {

        if ($this->commandData->getAddOn('datatables')) {

            $path = $this->commandData->config->pathDataTables;
            $fileName = $this->commandData->modelName . 'DataTable.php';

            if (file_exists($path . $fileName) && !$this->confirmOverwrite('DataTable ' . $fileName)) {

                return;
            }

            $this->controllerGenerator->callReflectionMethod('generateDataTable');
        }
    }

    /**
     * Generate controller
     *
     * @return void
     */
    public function generateController() {

        $path = $this->controllerGenerator->getReflectionProperty('path', true);
        $fileName = $this->controllerGenerator->getReflectionProperty('fileName', true);

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Controller ' . $fileName)) {

            return;
        }

        $this->controllerGenerator->generate();
    }	
	
	/**
     * Generate view
     *
     * @return void
	 */
    public function generateView() {

		$path = $this->viewGenerator->getReflectionProperty('path', true);
		 
		// pour l'instant on part du principe que si il y a un des fichier blade 
		// on demande si on veux refaire tout
		 $files = [
			ViewGenerator::TABLE_GENERATE_BLADE_FILE, 
			ViewGenerator::INDEX_GENERATE_BLADE_FILE,	 
			ViewGenerator::FIELDS_GENERATE_BLADE_FILE, 
			ViewGenerator::CREATE_GENERATE_BLADE_FILE, 
			ViewGenerator::EDIT_GENERATE_BLADE_FILE, 
			ViewGenerator::SHOW_GENERATE_BLADE_FILE, 
			ViewGenerator::SHOW_FIELDS_GENERATE_BLADE_FILE, 
			ViewGenerator::SHOW_FIELDS_GENERATE_BLADE_FILE
        ];
 
		$isOneFileExist = false;

        foreach ($files as $fileName) {

           if (file_exists($path . $fileName)) {

			 $isOneFileExist = true;
		   }
        }
		  
        if ($isOneFileExist && !$this->confirmOverwrite('Views')) {

            return;
        }

        $this->viewGenerator->generate();
	}

    /**
     * Generate policy
     *
     * @return void
     */
    public function generatePolicy() {

        $path = $this->policyGenerator->getReflectionProperty('path', true);
        $fileName = $this->policyGenerator->getReflectionProperty('fileName', true);

        if (file_exists($path . $fileName) && !$this->confirmOverwrite('Policy ' . $fileName)) {

            return;
        }

        $this->policyGenerator->generatePolicy();
    }
}
