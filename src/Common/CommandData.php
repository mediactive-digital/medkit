<?php

namespace MediactiveDigital\MedKit\Common;

use Illuminate\Console\Command;

use InfyOm\Generator\Common\CommandData as InfyOmCommandData;
use InfyOm\Generator\Common\TemplatesManager;
use InfyOm\Generator\Common\GeneratorField;
use InfyOm\Generator\Common\GeneratorFieldRelation;
use InfyOm\Generator\Utils\GeneratorFieldsInputUtil;

use MediactiveDigital\MedKit\Utils\TableFieldsGenerator;
use MediactiveDigital\MedKit\Traits\Reflection;

use File;
use Str;
use Schema;

class CommandData extends InfyOmCommandData {

    /** 
     * @var TableFieldsGenerator 
     */
    public $tableFieldsGenerator;

    /** 
     * @var GeneratorField[] 
     */
    public $formatedFields = [];

    /** 
     * @var array
     */
    public $timestamps;

    /** 
     * @var array
     */
    public $userStamps;

    /** 
     * @var string 
     */
    public $lastActivity;

    /** 
     * @var mixed 
     */
    public $model;

    use Reflection;

    /**
     * @param Command $commandObj
     * @param string $commandType
     * @param TemplatesManager $templatesManager
     */
    public function __construct(Command $commandObj, $commandType, TemplatesManager $templatesManager = null) {

        parent::__construct($commandObj, $commandType, $templatesManager);
    }

    public function initCommandData() {

        $this->config->init($this);

        $this->setDefaults();
        $this->setConfiguration();
    }

    /**
     * Set default values
     *
     * @return void
     */
    public function setDefaults() {

        $this->model = $this->getModel();

        if (!$this->getOption('tableName') && $this->model && !Schema::hasTable($this->dynamicVars['$TABLE_NAME$']) && ($table = $this->model->getTable())) {

            $this->config->tableName = $table;
            $this->addDynamicVariable('$TABLE_NAME$', $table);
            $this->addDynamicVariable('$TABLE_NAME_TITLE$', Str::studly($table));
            $this->setOption('tableName', $table);
        }

        $this->getTableFieldsGenerator();

        if (!$this->getOption('primary')) {

            $primary = Schema::hasTable($this->dynamicVars['$TABLE_NAME$']) ? $this->tableFieldsGenerator->getPrimaryKeyOfTable($this->dynamicVars['$TABLE_NAME$']) : '';
            $primary = $primary ?: ($this->model ? $this->model->getKeyName() : $primary);

            if ($primary) {

                $this->addDynamicVariable('$PRIMARY_KEY_NAME$', $primary);
                $this->setOption('primary', $primary);
            }
        }
    }

    /**
     * Set configuration
     *
     * @return void
     */
    public function setConfiguration() {

        // Initialization
        $nameSpacePrefix = $this->getNameSpacePrefix();
        $pathPrefix = $this->getPathPrefix();

        // Options
        $this->config->options['userStamps'] = config('infyom.laravel_generator.options.userStamps', false);
        $this->config->options['flashValidationErrors'] = config('infyom.laravel_generator.options.flashValidationErrors', false);

        // Dynamic variables
        $this->addDynamicVariable('$NAMESPACE_FORMS$', config('infyom.laravel_generator.namespace.forms', 'App\Forms') . $nameSpacePrefix);
        $this->addDynamicVariable('$BD_FIELD_CREATED_BY_NAME$', config('infyom.laravel_generator.user_stamps.created_by', 'created_by'));
        $this->addDynamicVariable('$NAMESPACE_POLICIES$', config('infyom.laravel_generator.namespace.policies', 'App\Policies') . $nameSpacePrefix);
        $this->addDynamicVariable('$TABLE_NAME_SINGULAR$', Str::singular($this->dynamicVars['$TABLE_NAME$']));

        // Add ons
        $this->config->addOns['forms'] = config('infyom.laravel_generator.add_on.forms', true);
        $this->config->addOns['permissions.superadmin_role_id'] = config('infyom.laravel_generator.add_on.permissions.superadmin_role_id', 1);
        $this->config->addOns['user_stamps.enabled'] = config('infyom.laravel_generator.add_on.user_stamps.enabled', true);
        $this->config->addOns['tracks_history.provider_file'] = config('infyom.laravel_generator.add_on.tracks_history.provider_file', 'AppServiceProvider.php');

        // Paths
        $this->config->pathForms = config('infyom.laravel_generator.path.forms', app_path('Forms/')) . $pathPrefix;
        $this->config->pathSchema = config('infyom.laravel_generator.path.schema_files', resource_path('model_schemas/'));
        $this->config->pathMiddlewares = config('infyom.laravel_generator.path.middlewares', app_path('Http/Middleware/')) . $pathPrefix;
        $this->config->pathPolicies = config('infyom.laravel_generator.path.policies', app_path('Policies/')) . $pathPrefix;
        $this->config->pathAuthProvider = config('infyom.laravel_generator.path.auth_provider', app_path('Providers/AuthServiceProvider.php'));
        $this->config->pathProviders = config('infyom.laravel_generator.path.providers', app_path('Providers/')) . $pathPrefix;

        // Others
        $this->timestamps = TableFieldsGenerator::getTimestampFieldNames();
        $this->userStamps = TableFieldsGenerator::getUserStampsFieldNames();
        $this->lastActivity = TableFieldsGenerator::getLastActivityFieldName();
    }

    public function getFields() {

        $this->fields = [];

        $schemaPath = config('infyom.laravel_generator.path.schema_files', resource_path('model_schemas/'));
        $fileName = $this->modelName . '.json';
        $filePath = $schemaPath . $fileName;
        $getInput = true;

        if (File::exists($filePath)) {

            $default = $this->getOption('fieldsFile') || $this->getOption('jsonFromGUI') || $this->getOption('fromTable') ? false : true;

            if ($this->commandObj->confirm('A schema file already exists (' . $filePath . '), do you wish to use it as source ?', $default)) {

                $this->setOption('fieldsFile', $fileName);
                $this->setOption('save', false);
                $this->callReflectionMethod('getInputFromFileOrJson');
            }
            else {

                $getInput = false;
            }
        }

        if ($getInput) {

            if ($this->getOption('fieldsFile') || $this->getOption('jsonFromGUI')) {

                $this->callReflectionMethod('getInputFromFileOrJson');
            } 
            elseif ($this->getOption('fromTable')) {

                $this->getInputFromTable();
            } 
            else {

                $this->getInputFromConsole();
            }
        }

        $this->formatInput();
    }

    /** 
     * Format input.
     *
     * @return void
     */
    private function formatInput() {

        foreach ($this->relations as $relation) {

            $column = isset($relation->inputs[1]) ? $relation->inputs[1] : '';

            if ($column) {

                foreach ($this->fields as $field) {

                    if ($field->name == $column) {

                        $field->relation = $relation;

                        break;
                    }
                }
            }
        }

        $this->formatedFields = $this->fields;
    }

    private function getInputFromConsole() {

        $this->commandInfo('Specify fields for the model (skip id & timestamp fields, we will add it automatically)');
        $this->commandInfo('Read docs carefully to specify field inputs)');
        $this->commandInfo('Enter "exit" to finish');

        $this->callReflectionMethod('addPrimaryKey');

        while (true) {

            $fieldInputStr = $this->commandObj->ask('Field: (name db_type html_type options)', '');

            if (empty($fieldInputStr) || $fieldInputStr == false || $fieldInputStr == 'exit') {

                break;
            }

            if (!GeneratorFieldsInputUtil::validateFieldInput($fieldInputStr)) {

                $this->commandError('Invalid Input. Try again');

                continue;
            }

            $validations = $this->commandObj->ask('Enter validations: ', false);
            $validations = $validations == false ? [] : explode('|', $validations);

            if ($this->getOption('relations')) {

                $relation = $this->commandObj->ask('Enter relationship (Leave Blank to skip):', false);
            } 
            else {

                $relation = '';
            }

            $this->fields[] = GeneratorFieldsInputUtil::processFieldInput($fieldInputStr, $validations);

            if (!empty($relation)) {

                $this->relations[] = GeneratorFieldRelation::parseRelation($relation);
            }
        }

        if (config('infyom.laravel_generator.timestamps.enabled', true)) {

            $this->addTimestamps();
        }

        if (config('infyom.laravel_generator.user_stamps.enabled', true)) {

            $this->addUserStamps();
        }
    }

    private function addTimestamps() {

        $createdAt = new GeneratorField();
        $createdAt->name = $this->timestamps[0];
        $createdAt->parseDBType('timestamp');
        $createdAt->parseOptions('s,f,if,ii');

        $this->fields[] = $createdAt;

        $updatedAt = new GeneratorField();
        $updatedAt->name = $this->timestamps[1];
        $updatedAt->parseDBType('timestamp');
        $updatedAt->parseOptions('s,f,if,ii');

        $this->fields[] = $updatedAt;

        if ($this->getOption('softDelete')) {

            $deletedAt = new GeneratorField();
            $deletedAt->name = $this->timestamps[2];
            $deletedAt->parseDBType('timestamp');
            $deletedAt->parseOptions('s,f,if,ii');

            $this->fields[] = $deletedAt;
        }
    }

    /**
     * Add user stamps to input
     *
     * @return void
     */
    private function addUserStamps() {

        $createdBy = new GeneratorField();
        $createdBy->name = $this->userStamps[0];
        $createdBy->parseDBType('number');
        $createdBy->parseOptions('s,f,if,ii');

        $this->fields[] = $createdBy;

        $updatedBy = new GeneratorField();
        $updatedBy->name = $this->userStamps[1];
        $updatedBy->parseDBType('number');
        $updatedBy->parseOptions('s,f,if,ii');

        $this->fields[] = $updatedBy;

        if ($this->getOption('softDelete')) {

            $deletedBy = new GeneratorField();
            $deletedBy->name = $this->userStamps[2];
            $deletedBy->parseDBType('number');
            $deletedBy->parseOptions('s,f,if,ii');

            $this->fields[] = $deletedBy;
        }
    }

    private function getInputFromTable() {

        $this->tableFieldsGenerator->prepareRelations();
        $this->tableFieldsGenerator->prepareFieldsFromTable();

        $this->fields = $this->tableFieldsGenerator->fields;
        $this->relations = $this->tableFieldsGenerator->relations;
    }

    /**
     * Get namespace prefix
     *
     * @return string $prefix
     */
    public function getNameSpacePrefix(): string {

        $prefix = $this->config->prefixes['ns'] ?: '';

        if (!empty($prefix)) {

            $prefix = '\\' . $prefix;
        }

        return $prefix;
    }

    /**
     * Get path prefix
     *
     * @return string $prefix
     */
    public function getPathPrefix(): string {

        $prefix = $this->config->prefixes['path'] ?: '';

        if (!empty($prefix)) {

            $prefix .= '/';
        }

        return $prefix;
    }

    /**
     * Get model
     *
     * @return mixed|null $model
     */
    public function getModel() {

        $class = '\\' . $this->config->nsModel . '\\' . $this->modelName;
        $model = class_exists($class) ? new $class : null;

        return $model;
    }

    /**
     * Get table fields generator
     *
     * @return \MediactiveDigital\MedKit\Utils\TableFieldsGenerator
     */
    public function getTableFieldsGenerator() {

        $ignoredFields = ($ignoredFields = $this->getOption('ignoreFields')) ? explode(',', trim($ignoredFields)) : [];

        $this->tableFieldsGenerator = new TableFieldsGenerator($this->dynamicVars['$TABLE_NAME$'], $ignoredFields, $this->config->connection);
    }
}
