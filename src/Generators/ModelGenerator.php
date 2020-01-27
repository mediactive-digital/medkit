<?php

namespace MediactiveDigital\MedKit\Generators;

use InfyOm\Generator\Generators\ModelGenerator as InfyOmModelGenerator;
use InfyOm\Generator\Generators\SwaggerGenerator;
use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Common\GeneratorField;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;
use MediactiveDigital\MedKit\Helpers\Helper;

use Str;

class ModelGenerator extends InfyOmModelGenerator {

    use Reflection;

    /** 
     * @var CommandData 
     */
    private $commandData;

    /** 
     * @var string 
     */
    private $path;

    /** 
     * @var string 
     */
    private $fileName;

    /** 
     * @var array
     */
    private $timestamps;

    /** 
     * @var array
     */
    private $userStamps;

    /** 
     * @var string 
     */
    private $lastActivity;

    /** 
     * @var bool 
     */
    private $hasTimestamps;

    /** 
     * @var bool 
     */
    private $hasSoftDelete;

    /** 
     * @var bool 
     */
    private $hasUserStamps;

    /** 
     * @var bool 
     */
    private $hasTranslatable;

    /**
     * ModelGenerator constructor.
     *
     * @param CommandData $commandData
     */
    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path');
        $this->fileName = $this->getReflectionProperty('fileName');
        $this->timestamps = $this->commandData->timestamps;
        $this->userStamps = $this->commandData->userStamps;
        $this->lastActivity = $this->commandData->lastActivity;
        $this->hasTimestamps = Helper::modelHasTimestamps($this->commandData);
        $this->hasSoftDelete = Helper::modelHasSoftDelete($this->commandData);
        $this->hasUserStamps = Helper::modelHasUserStamps($this->commandData);
        $this->hasTranslatable = Helper::modelHasTranslatable($this->commandData);
    }

    public function generate() {

        $templateData = get_template('model.model', 'laravel-generator');
        $templateData = $this->fillTemplate($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nModel created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    public function generateCasts() {

        $casts = [];
        $ignore = array_merge($this->timestamps, [$this->lastActivity]);

        foreach ($this->commandData->fields as $field) {

            if (in_array($field->name, $ignore)) {

                continue;
            }

            $rule = "'" . $field->name . "' => ";

            switch (strtolower($field->fieldType)) {

                case 'integer' :
                case 'increments' :
                case 'smallinteger' :
                case 'long' :
                case 'biginteger' :

                    $rule .= "'integer'";

                break;

                case 'double' :

                    $rule .= "'double'";

                break;

                case 'float' :
                case 'decimal' :

                    $rule .= "'float'";
                break;

                case 'boolean' :

                $rule .= "'boolean'";

                break;

                case 'datetime' :
                case 'datetimetz' :

                    $rule .= "'datetime'";

                break;

                case 'date' :

                    $rule .= "'date'";

                break;

                case 'enum' :
                case 'string' :
                case 'char' :
                case 'text' :

                    $rule .= "'string'";

                break;

                case 'json' :

                    if (Helper::isJsonField($field)) {

                        $rule .= "'array'";

                        if ($this->hasTranslatable && Helper::isTranslatableField($field)) {

                            $rule = '';
                        }
                    }

                break;

                default:
                    
                    $rule = '';

                break;
            }

            if (!empty($rule)) {

                $casts[] = $rule;
            }
        }

        return $casts;
    }

    /**
     * Generate mutators.
     *
     * @return array $mutators
     */
    private function generateMutators(): array {

        $mutators = [];
        $template = get_template('model.mutator');
        $ignore = array_merge($this->timestamps, [$this->lastActivity]);

        foreach ($this->commandData->fields as $field) {

            if ($field->htmlType == 'datetime-local' && !in_array($field->name, $ignore)) {

                $mutator = str_replace('$ATTRIBUTE_NAME$', $field->name, $template);
                $mutator = str_replace('$ATTRIBUTE_TYPE$', 'string', $mutator);
                $mutator = str_replace('$ATTRIBUTE_TYPE_HINT$', 'string', $mutator);
                $mutator = str_replace('$ATTRIBUTE_NAME_PASCAL$', Str::ucfirst(Str::camel($field->name)), $mutator);
                $mutator = str_replace('$MUTATION_FUNCTION$', 'Carbon::parse($value)->format(\'Y-m-d H:i:s\')', $mutator);

                $mutators[] = $mutator;
            }
        }

        return $mutators;
    }

    private function fillTemplate($templateData) {

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = $this->fillSoftDeletes($templateData);
        $templateData = $this->fillUserStamps($templateData);
        $templateData = $this->fillTranslatables($templateData);

        $fillables = [];

        foreach ($this->commandData->fields as $field) {

            if ($field->isFillable) {

                $fillables[] = "'" . $field->name . "'";
            }
        }

        $templateData = $this->callReflectionMethod('fillDocs', $templateData);
        $templateData = $this->fillTimestamps($templateData);

        $primaryKey = $this->commandData->getOption('primary') ?: '';
        $primary = $primaryKey ? infy_tab() . "protected \$primaryKey = '" . $primaryKey . "';\n" : "";

        $templateData = str_replace('$PRIMARY$', $primary, $templateData);
        $templateData = str_replace('$FIELDS$', implode(',' . infy_nl_tab(1, 2), $fillables), $templateData);
        $templateData = str_replace('$CAST$', implode(',' . infy_nl_tab(1, 2), $this->generateCasts()), $templateData);
        $templateData = str_replace('$MUTATORS$', implode(FormatHelper::NEW_LINE, $this->generateMutators()), $templateData);
        $templateData = str_replace('$RELATIONS$', fill_template($this->commandData->dynamicVars, implode(FormatHelper::NEW_LINE, $this->callReflectionMethod('generateRelations'))), $templateData);
        $templateData = str_replace('$GENERATE_DATE$', date('F j, Y, g:i a T'), $templateData);
        $templateData = FormatHelper::cleanTemplate($templateData);

        return $templateData;
    }

    private function fillTimestamps($templateData) {

        if ($this->hasTimestamps) {

            list($created_at, $updated_at, $deleted_at) = collect($this->timestamps)->map(function($field) {

                return !empty($field) ? "'$field'" : 'null';
            });

            $replace = infy_nl_tab() . "const CREATED_AT = $created_at;";
            $replace .= infy_nl_tab() . "const UPDATED_AT = $updated_at;";

            if ($this->hasSoftDelete) {

                $replace .= infy_nl_tab() . "const DELETED_AT = $deleted_at;";
            }
        }
        else {

            $replace = infy_nl_tab() . 'public $timestamps = false;';
        }

        $replace .= "\n";

        return str_replace('$TIMESTAMPS$', $replace, $templateData);
    }

    private function fillSoftDeletes($templateData) {

        $softDeleteImport = $softDelete = $softDeleteDates = '';
        $timestamps = [];

        if ($this->hasTimestamps) {

            $timestamps = [$this->timestamps[0], $this->timestamps[1]];

            if ($this->hasSoftDelete) {

                $timestamps = $this->timestamps;

                $softDeleteImport = "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n";
                $softDelete = infy_tab() . 'use SoftDeletes;';
            }
        }

        foreach ($this->commandData->fields as $field) {

            if (!in_array($field->name, $timestamps) && strpos($field->htmlType, 'date') !== false) {

                array_unshift($timestamps , $field->name);
            }
        }

        if ($timestamps) {

            $softDeleteDates = infy_nl_tab() . 'protected $dates = ' . FormatHelper::writeValueToPhp($timestamps, 1) . ';';
        }

        $templateData = str_replace('$SOFT_DELETE_IMPORT$', $softDeleteImport, $templateData);
        $templateData = str_replace('$SOFT_DELETE$', $softDelete, $templateData);
        $templateData = str_replace('$SOFT_DELETE_DATES$', $softDeleteDates, $templateData);

        return $templateData;
    }

    private function fillUserStamps($templateData) {

        $userStampsImport = $userStamps = $userStampsConstants = '';

        if ($this->hasUserStamps) {

            $userStampsImport = "use Wildside\\Userstamps\\Userstamps;\n";
            $userStamps = infy_tab() . 'use Userstamps;';

            $userStampsConstants = [
                'const CREATED_BY = \'' . $this->userStamps[0] . '\';',
                'const UPDATED_BY = \'' . $this->userStamps[1] . '\';'
            ];

            if ($this->hasSoftDelete) {

                $userStampsConstants[] = 'const DELETED_BY = \'' . $this->userStamps[2] . '\';';
            }

            $userStampsConstants = implode(infy_nl_tab(1, 1), $userStampsConstants);
        }

        $templateData = str_replace('$USER_STAMPS_IMPORT$', $userStampsImport, $templateData);
        $templateData = str_replace('$USER_STAMPS$', $userStamps, $templateData);
        $templateData = str_replace('$USER_STAMPS_CONSTANTS$', $userStampsConstants, $templateData);

        return $templateData;
    }

    /**
     * Fill translatables
     *
     * @param string $templateData
     * @return string $templateData
     */
    private function fillTranslatables(string $templateData): string {

        $translatableImport = $translatable = $translatableFields = '';

        if ($this->hasTranslatable) {

            $translatableImport = "use Spatie\\Translatable\\HasTranslations;\n";
            $translatable = infy_tab() . 'use HasTranslations;';
            $translatableFields = [];

            foreach ($this->commandData->fields as $field) {

                if (Helper::isTranslatableField($field)) {

                    $translatableFields[] = $field->name;
                }
            }

            $translatableFields = infy_nl_tab() . 'public $translatable = ' . FormatHelper::writeValueToPhp($translatableFields, 1) . ';' . infy_nl_tab();
        }

        $templateData = str_replace('$TRANSLATABLE_IMPORT$', $translatableImport, $templateData);
        $templateData = str_replace('$TRANSLATABLE$', $translatable, $templateData);
        $templateData = str_replace('$TRANSLATABLE_FIELDS$', $translatableFields, $templateData);

        return $templateData;
    }

    private function fillDocs($templateData) {

        if ($this->commandData->getAddOn('swagger')) {

            $templateData = $this->generateSwagger($templateData);
        }

        $docsTemplate = get_template('docs.model');
        $docsTemplate = fill_template($this->commandData->dynamicVars, $docsTemplate);

        $fillables = '';
        $fieldsArr = [];
        $count = 1;

        foreach ($this->commandData->relations as $relation) {

            $field = $relationText = (isset($relation->inputs[0])) ? $relation->inputs[0] : null;

            if (in_array($field, $fieldsArr)) {

                $relationText = $relationText . '_' . $count;

                $count++;
            }

            $fillables .= ' * @property ' . $this->getPHPDocType($relation->type, $relation, $relationText) . FormatHelper::NEW_LINE;
            $fieldsArr[] = $field;
        }

        foreach ($this->commandData->fields as $field) {

            if ($field->isFillable) {

                $fillables .= ' * @property ' . $this->getPHPDocType($field->fieldType) . ' ' . $field->name . FormatHelper::NEW_LINE;
            }
        }

        $docsTemplate = str_replace('$GENERATE_DATE$', date('F j, Y, g:i a T'), $docsTemplate);
        $docsTemplate = str_replace('$PHPDOC$', $fillables, $docsTemplate);
        $templateData = str_replace('$DOCS$', $docsTemplate, $templateData);

        return $templateData;
    }

    /**
     * @param $db_type
     * @param GeneratorFieldRelation|null $relation
     * @param string|null $relationText
     *
     * @return string
     */
    private function getPHPDocType($db_type, $relation = null, $relationText = null) {

        $relationText = (!empty($relationText)) ? $relationText : null;

        switch ($db_type) {

            case 'datetime' :

                return 'string|\Carbon\Carbon';

            case 'json' :

                return 'string|array';

            case '1t1' :

                return '\\' . $this->commandData->config->nsModel . '\\' . $relation->inputs[0] . ' ' . Str::camel($relationText);

            case 'mt1' :

                if (isset($relation->inputs[1])) {

                    $relationName = str_replace('_id', '', strtolower($relation->inputs[1]));
                } 
                else {

                    $relationName = $relationText;
                }

                return '\\' . $this->commandData->config->nsModel . '\\' . $relation->inputs[0] . ' ' . Str::camel($relationName);

            case '1tm' :
            case 'mtm' :
            case 'hmt' :

                return '\Illuminate\Database\Eloquent\Collection ' . Str::camel(Str::plural($relationText));

            default :

                $fieldData = SwaggerGenerator::getFieldType($db_type);

                if (!empty($fieldData['fieldType'])) {

                    return $fieldData['fieldType'];
                }

                return $db_type;
        }
    }
}
