<?php

namespace MediactiveDigital\MedKit\Generators;

use InfyOm\Generator\Generators\ModelGenerator as InfyOmModelGenerator;
use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

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
        $templateData = str_replace('$MUTATORS$', implode(PHP_EOL, $this->generateMutators()), $templateData);
        $templateData = str_replace('$RELATIONS$', fill_template($this->commandData->dynamicVars, implode(PHP_EOL, $this->callReflectionMethod('generateRelations'))), $templateData);
        $templateData = str_replace('$GENERATE_DATE$', date('F j, Y, g:i a T'), $templateData);

        return $templateData;
    }

    private function fillTimestamps($templateData) {

        $replace = '';

        if (empty($this->timestamps)) {

            $replace = infy_nl_tab() . "public \$timestamps = false;";
        }
        else if ($this->commandData->getOption('fromTable')) {

            list($created_at, $updated_at, $deleted_at) = collect($this->timestamps)->map(function($field) {

                return !empty($field) ? "'$field'" : 'null';
            });

            $replace .= infy_nl_tab() . "const CREATED_AT = $created_at;";
            $replace .= infy_nl_tab() . "const UPDATED_AT = $updated_at;";

            $hasSoftDelete = $this->hasSoftDelete();

            if ($hasSoftDelete) {

                $replace .= infy_nl_tab() . "const DELETED_AT = $deleted_at;";
            }
        }

        $replace .= $replace ? "\n" : "";

        return str_replace('$TIMESTAMPS$', $replace, $templateData);
    }

    private function fillSoftDeletes($templateData) {

        $softDeleteImport = $softDelete = $softDeleteDates = '';
        $timestamps = $this->timestamps;

        if ($timestamps) {

            $hasSoftDelete = $this->hasSoftDelete();

            if ($hasSoftDelete) {

                $softDeleteImport = "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n";
                $softDelete = infy_tab() . "use SoftDeletes;";
            }
            else {

                unset($timestamps[2]);
            }
        }

        foreach ($this->commandData->fields as $field) {

            if (!in_array($field->name, $timestamps) && strpos($field->htmlType, 'date') !== false) {

                $timestamps[] = $field->name;
            }
        }

        if ($timestamps) {

            $softDeleteDates = infy_nl_tab() . "protected \$dates = " . FormatHelper::writeValueToPhp($timestamps, 1) . ";";
        }

        $templateData = str_replace('$SOFT_DELETE_IMPORT$', $softDeleteImport, $templateData);
        $templateData = str_replace('$SOFT_DELETE$', $softDelete, $templateData);
        $templateData = str_replace('$SOFT_DELETE_DATES$', $softDeleteDates, $templateData);

        return $templateData;
    }

    private function fillUserStamps($templateData) {

        $userStampsImport = $userStamps = $userStampsConstants = '';
        $hasUserStamps = $this->hasUserStamps();

        if ($hasUserStamps) {

            $userStampsImport = "use Wildside\\Userstamps\\Userstamps;\n";
            $userStamps = infy_tab() . "use Userstamps;";

            $userStampsConstants = [
                "const CREATED_BY = '" . $this->userStamps[0] . "';",
                "const UPDATED_BY = '" . $this->userStamps[1] . "';"
            ];

            $hasSoftDelete = $this->hasSoftDelete();

            if ($hasSoftDelete) {

                $userStampsConstants[] = "const DELETED_BY = '" . $this->userStamps[2] . "';";
            }

            $userStampsConstants = implode(infy_nl_tab(1, 1), $userStampsConstants);
        }

        $templateData = str_replace('$USER_STAMPS_IMPORT$', $userStampsImport, $templateData);
        $templateData = str_replace('$USER_STAMPS$', $userStamps, $templateData);
        $templateData = str_replace('$USER_STAMPS_CONSTANTS$', $userStampsConstants, $templateData);

        return $templateData;
    }

    /**
     * Check if model has soft delete
     *
     * @return bool $softDelete
     */
    private function hasSoftDelete() {

        $softDelete = false;

        if ($this->commandData->getOption('softDelete') && $this->timestamps) {

            foreach ($this->commandData->fields as $field) {

                if ($field->name == $this->timestamps[2]) {

                    $softDelete = true;

                    break;
                }
            }
        }

        return $softDelete;
    }

    /**
     * Check if model has user stamps
     *
     * @return bool $userStamps
     */
    private function hasUserStamps() {

        $userStamps = false;

        if ($this->commandData->getOption('userStamps') && $this->userStamps) {

            foreach ($this->commandData->fields as $field) {

                if ($field->name == $this->userStamps[0]) {

                    $userStamps = true;

                    break;
                }
            }
        }

        return $userStamps;
    }
}
