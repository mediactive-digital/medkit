<?php

namespace MediactiveDigital\MedKit\Common;

use InfyOm\Generator\Common\CommandData as InfyOmCommandData;
use InfyOm\Generator\Common\GeneratorField;
use InfyOm\Generator\Utils\GeneratorFieldsInputUtil;

use MediactiveDigital\MedKit\Utils\TableFieldsGenerator;
use MediactiveDigital\MedKit\Traits\Reflection;

class CommandData extends InfyOmCommandData {

    /** 
     * @var TableFieldsGenerator 
     */
    public $tableFieldsGenerator;

    use Reflection;

    public function getFields() {

        $this->fields = [];

        if ($this->getOption('fieldsFile') or $this->getOption('jsonFromGUI')) {

            $this->callReflectionMethod('getInputFromFileOrJson');
        } 
        elseif ($this->getOption('fromTable')) {

            $this->getInputFromTable();
        } 
        else {

            $this->getInputFromConsole();
        }
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
            $validations = $validations == false ? '' : $validations;

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
        $createdAt->name = 'created_at';
        $createdAt->parseDBType('timestamp');
        $createdAt->parseOptions('s,f,if,ii');

        $this->fields[] = $createdAt;

        $updatedAt = new GeneratorField();
        $updatedAt->name = 'updated_at';
        $updatedAt->parseDBType('timestamp');
        $updatedAt->parseOptions('s,f,if,ii');

        $this->fields[] = $updatedAt;

        if ($this->getOption('softDelete')) {

            $deletedAt = new GeneratorField();
            $deletedAt->name = 'deleted_at';
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
        $createdBy->name = 'created_by';
        $createdBy->parseDBType('number');
        $createdBy->parseOptions('s,f,if,ii');

        $this->fields[] = $createdBy;

        $updatedBy = new GeneratorField();
        $updatedBy->name = 'updated_by';
        $updatedBy->parseDBType('number');
        $updatedBy->parseOptions('s,f,if,ii');

        $this->fields[] = $updatedBy;

        if ($this->getOption('softDelete')) {

            $deletedBy = new GeneratorField();
            $deletedBy->name = 'deleted_by';
            $deletedBy->parseDBType('number');
            $deletedBy->parseOptions('s,f,if,ii');

            $this->fields[] = $deletedBy;
        }
    }

    private function getInputFromTable() {

        $tableName = $this->dynamicVars['$TABLE_NAME$'];

        $ignoredFields = $this->getOption('ignoreFields');

        if (!empty($ignoredFields)) {

            $ignoredFields = explode(',', trim($ignoredFields));
        } 
        else {
            
            $ignoredFields = [];
        }

        $this->tableFieldsGenerator = new TableFieldsGenerator($tableName, $ignoredFields, $this->config->connection);
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
    public function getNameSpacePrefix() {

        $prefix = $this->config->prefixes['ns'];

        if (!empty($prefix)) {

            $prefix = '\\' . $prefix;
        }

        return $prefix;
    }
}
