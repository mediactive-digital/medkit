<?php

namespace MediactiveDigital\MedKit\Utils;

use InfyOm\Generator\Utils\TableFieldsGenerator as InfyOmTableFieldsGenerator;

use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

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

    /** 
     * @var string 
     */
    public $lastActivity;

    public function __construct($tableName, $ignoredFields, $connection = '') {

        parent::__construct($tableName, $ignoredFields, $connection);

        $this->schemaManager = $this->getReflectionProperty('schemaManager');
        $this->columns = $this->getReflectionProperty('columns');

        $platform = $this->schemaManager->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('json', 'json');

        $columns = $this->schemaManager->listTableColumns($tableName);

        $this->columns = [];

        foreach ($columns as $column) {

            if (!in_array($column->getName(), $ignoredFields)) {

                $this->columns[] = $column;
            }
        }

        $this->setReflectionProperty('schemaManager', $this->schemaManager);
        $this->setReflectionProperty('columns', $this->columns);

        $this->userStamps = static::getUserStampsFieldNames();
        $this->lastActivity = static::getLastActivityFieldName();
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

                case 'json' :

                    $field = $this->callReflectionMethod('generateField', $column, 'json', 'textarea');

                break;

                default :

                    $field = $this->callReflectionMethod('generateField', $column, 'string', 'text');

                break;
            }

            $field->isNotNull = (bool)$column->getNotNull();

            // Get comments from table
            $field->description = $column->getComment();

            if (!$field->isPrimary) {

                $lower = strtolower($field->name);

                if (in_array($field->name, $this->timestamps) || in_array($field->name, $this->userStamps) || $field->name == $this->lastActivity || strpos($lower, 'token') !== false) {

                    $field->isSearchable = false;
                    $field->isFillable = false;
                    $field->inForm = false;
                    $field->inIndex = false;
                    $field->inView = false;
                }
                else {

                    $validations = [];
                    $isPassword = strpos($lower, 'password') !== false;

                    if ($field->isNotNull && !in_array($field->name, $dontRequireFields)) {

                        $validations[] = 'required';
                    }

                    if ($field->htmlType == 'number' && $column->getUnsigned()) {

                        $min = 0;

                        foreach ($this->relations as $relation) {

                            if ($relation->type == 'mt1' && $relation->inputs[1] == $field->name) {

                                $min = 1;

                                break;
                            }
                        }

                        $validations[] = 'min:' . ($min);
                    }
                    else if (!$isPassword && $field->htmlType == 'text' && ($max = $column->getLength())) {

                        $validations[] = 'max:' . $max;
                    }
                    else if ($field->htmlType == 'checkbox') {

                        $validations[] = 'boolean';
                    }

                    if ($isPassword) {

                        $field->htmlType = 'password';
                        $field->isSearchable = false;
                        $field->inIndex = false;
                        $field->inView = false;

                        $validations[] = 'min:8';
                        $validations[] = 'max:120';
                        $validations[] = 'confirmed';
                        $validations[] = 'regex:' . FormatHelper::PASSWORD_REGEX;
                    } 
                    else if (strpos($lower, 'email') !== false) {

                        $field->htmlType = $validations[] = 'email';
                    } 
                    else if (strpos($lower, 'phone') !== false) {

                        $field->htmlType = 'tel';
                    }

                    if ($type == 'json') {

                        $validations[] = 'array';
                    }

                    $indexes = $this->schemaManager->listTableDetails($this->tableName)->getIndexes();
                    $primaryKey = isset($indexes['primary']) ? $indexes['primary']->getColumns()[0] : '';

                    foreach ($indexes as $index) {

                        if ($index->isUnique()) {

                            $columns = $index->getColumns();

                            if (in_array($field->name, $columns)) {

                                if ($field->name != $columns[0]) {

                                    usort($columns, function ($columnA, $columnB) use (&$field) {

                                        return $columnA == $field->name ? -1 : ($columnB == $field->name ? 1 : 0);
                                    });
                                }

                                $uniqueValidation = 'unique:' . $this->tableName . ',' . $columns[0] . ',' . ($primaryKey ? '$this->' . $primaryKey . ',' . $primaryKey : 'null,null');

                                for ($i = 1; $i < count($columns); $i++) {

                                    $uniqueValidation .= ',' . $columns[$i] . ',$this->' . $columns[$i];
                                }

                                $validations[] = $uniqueValidation;

                                break;
                            }
                        }
                    }

                    $field->validations = $validations ?: $field->validations;
                }
            }

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

    /**
     * Get last activity column from config.
     *
     * @return string last_activity column name
     */
    public static function getLastActivityFieldName() {

        if (!config('infyom.laravel_generator.gdpr.enabled', true)) {

            return '';
        }

        $lastActivityName = config('infyom.laravel_generator.gdpr.last_activity', 'last_activity');

        return $lastActivityName;
    }
}
