<?php

namespace MediactiveDigital\MedKit\Utils;

use InfyOm\Generator\Utils\TableFieldsGenerator as InfyOmTableFieldsGenerator;

use MediactiveDigital\MedKit\Traits\Reflection;

use Str;

class TableFieldsGenerator extends InfyOmTableFieldsGenerator {

    use Reflection;

    /** 
     * @var AbstractSchemaManager 
     */
    private $schemaManager;

    /** 
     * @var Column[] 
     */
    private $columns;

    /** 
     * @var array 
     */
    public $userStamps;

    public function __construct($tableName, $ignoredFields, $connection = '') {

        parent::__construct($tableName, $ignoredFields, $connection);

        $this->schemaManager = $this->getReflectionProperty('schemaManager');
        $this->columns = $this->getReflectionProperty('columns');
        $this->userStamps = static::getUserStampsFieldNames();
    }

    /**
     * Prepares array of GeneratorField from table columns.
     */
    public function prepareFieldsFromTable() {

        foreach ($this->columns as $column) {

            $type = $column->getType()->getName();

            switch ($type) {

                case 'integer' :
                
                    $field = $this->callReflectionMethod('generateIntFieldInput', $column, 'integer');

                break;

                case 'smallint' :

                    $field = $this->callReflectionMethod('generateIntFieldInput', $column, 'smallInteger');

                break;

                case 'bigint' :

                    $field = $this->callReflectionMethod('generateIntFieldInput', $column, 'bigInteger');

                break;

                case 'boolean' :

                    $name = Str::title(str_replace('_', ' ', $column->getName()));
                    $field = $this->callReflectionMethod('generateField', $column, 'boolean', 'checkbox,1');

                break;

                case 'datetime' :

                    $field = $this->callReflectionMethod('generateField', $column, 'datetime', 'date');

                break;

                case 'datetimetz' :

                    $field = $this->callReflectionMethod('generateField', $column, 'dateTimeTz', 'date');

                break;

                case 'date' :

                    $field = $this->callReflectionMethod('generateField', $column, 'date', 'date');

                break;

                case 'time' :

                    $field = $this->callReflectionMethod('generateField', $column, 'time', 'text');

                break;

                case 'decimal' :

                    $field = $this->callReflectionMethod('generateNumberInput', $column, 'decimal');

                break;

                case 'float' :

                    $field = $this->callReflectionMethod('generateNumberInput', $column, 'float');

                break;

                case 'string' :

                    $field = $this->callReflectionMethod('generateField', $column, 'string', 'text');

                break;

                case 'text' :

                    $field = $this->callReflectionMethod('generateField', $column, 'text', 'textarea');

                break;

                default :

                    $field = $this->callReflectionMethod('generateField', $column, 'string', 'text');

                break;
            }

            $lower = strtolower($field->name);

            if ($lower == 'password') {

                $field->htmlType = 'password';
            } 
            elseif ($lower == 'email') {

                $field->htmlType = 'email';
            } 
            elseif (in_array($field->name, $this->timestamps) || in_array($field->name, $this->userStamps)) {

                $field->isSearchable = false;
                $field->isFillable = false;
                $field->inForm = false;
                $field->inIndex = false;
                $field->inView = false;
            }

            $field->isNotNull = (bool)$column->getNotNull();

            // Get comments from table
            $field->description = $column->getComment();

            $this->fields[] = $field;
        }
    }

    /**
     * Get user stamps columns from config.
     *
     * @return array the set of [created_by column name, updated_by column name, deleted_by column name]
     */
    public static function getUserStampsFieldNames() {

        if (!config('infyom.laravel_generator.user_stamps.enabled', true)) {

            return [];
        }

        $createdByName = config('infyom.laravel_generator.timestamps.created_by', 'created_by');
        $updatedByName = config('infyom.laravel_generator.timestamps.updated_by', 'updated_by');
        $deletedByName = config('infyom.laravel_generator.timestamps.deleted_by', 'deleted_by');

        return [$createdByName, $updatedByName, $deletedByName];
    }
}
