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

        $dontRequireFields = config('infyom.laravel_generator.options.hidden_fields', []) + config('infyom.laravel_generator.options.excluded_fields', []);

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

                    $field = $this->callReflectionMethod('generateField', $column, 'datetime', 'datetime-local');

                break;

                case 'datetimetz' :

                    $field = $this->callReflectionMethod('generateField', $column, 'dateTimeTz', 'datetime-local');

                break;

                case 'date' :

                    $field = $this->callReflectionMethod('generateField', $column, 'date', 'date');

                break;

                case 'time' :

                    $field = $this->callReflectionMethod('generateField', $column, 'time', 'time');

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

            $field->isNotNull = (bool)$column->getNotNull();

            if (!$field->isPrimary && $field->isNotNull && !in_array($field->name, $dontRequireFields)) {

                $field->validations .= 'required';
            }

            if ($field->htmlType == 'text' && ($max = $column->getLength())) {

                $field->validations .= ($field->validations ? '|' : '') . 'max:' . $max;
            }
            else if ($field->htmlType == 'number' && $column->getUnsigned()) {

                $min = $field->isPrimary || in_array($field->name, $this->userStamps) ? 1 : 0;

                if (!$min) {

                    foreach ($this->relations as $relation) {

                        if ($relation->type == 'mt1' && $relation->inputs[1] == $field->name) {

                            $min = 1;
                        }
                    }
                }

                $field->validations .= ($field->validations ? '|' : '') . 'min:' . ($min);
            }

            if (!$field->isPrimary) {

                $indexes = $this->schemaManager->listTableDetails($this->tableName)->getIndexes();

                foreach ($indexes as $index) {

                    if ($index->isUnique()) {

                        $columns = $index->getColumns();

                        if (in_array($field->name, $columns)) {

                            $field->validations .= ($field->validations ? '|' : '') . 'unique:' . $this->tableName;
                            $count = count($columns);

                            if ($count > 1) {

                                $field->validations .= ',' . $columns[0] . ',$this->' . $columns[0] . ',id,' . $columns[1] . ',$this->' . $columns[1];
                            }

                            for ($i = 2; $i < $count; $i++) {

                                $field->validations .= ',' . $columns[$i] . ',$this->' . $columns[$i];
                            }

                            break;
                        }
                    }
                }
            }

            $lower = strtolower($field->name);

            if (strpos($lower, 'password') !== false) {

                $field->htmlType = 'password';
            } 
            else if (strpos($lower, 'email') !== false) {

                $field->htmlType = 'email';
            } 
            else if (strpos($lower, 'phone') !== false) {

                $field->htmlType = 'tel';
            } 
            else if (in_array($field->name, $this->timestamps) || in_array($field->name, $this->userStamps)) {

                $field->isSearchable = false;
                $field->isFillable = false;
                $field->inForm = false;
                $field->inIndex = false;
                $field->inView = false;
            }

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
