<?php

namespace App\Generators;

use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Generators\ModelGenerator as InfyOmModelGenerator;

use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Utils\TableFieldsGenerator;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

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
     * ModelGenerator constructor.
     *
     * @param CommandData $commandData
     */
    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $commandData->config->options['userStamps'] = config('infyom.laravel_generator.options.userStamps', false);

        $this->commandData = $commandData;
        $this->path = $commandData->config->pathModel;
        $this->fileName = $this->commandData->modelName . '.php';
    }

    public function generate() {

        $this->timestamps = TableFieldsGenerator::getTimestampFieldNames();
        $this->userStamps = TableFieldsGenerator::getUserStampsFieldNames();

        $templateData = get_template('model.model', 'laravel-generator');
        $templateData = $this->fillTemplate($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nModel created: ");
        $this->commandData->commandInfo($this->fileName);
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

        $templateData = $this->call('fillDocs', $templateData);
        $templateData = $this->fillTimestamps($templateData);

        if ($this->commandData->getOption('primary')) {

            $primary = infy_tab() . "protected \$primaryKey = '" . $this->commandData->getOption('primary') . "';\n";
        } 
        else {

            $primary = '';
        }

        $templateData = str_replace('$PRIMARY$', $primary, $templateData);
        $templateData = str_replace('$FIELDS$', implode(',' . infy_nl_tab(1, 2), $fillables), $templateData);
        $templateData = str_replace('$RULES$', implode(',' . infy_nl_tab(1, 2), $this->call('generateRules')), $templateData);
        $templateData = str_replace('$CAST$', implode(',' . infy_nl_tab(1, 2), $this->generateCasts()), $templateData);
        $templateData = str_replace('$RELATIONS$', fill_template($this->commandData->dynamicVars, implode(PHP_EOL.infy_nl_tab(1, 1), $this->call('generateRelations'))), $templateData);
        $templateData = str_replace('$GENERATE_DATE$', date('F j, Y, g:i a T'), $templateData);

        return $templateData;
    }

    private function fillTimestamps($templateData) {

        $replace = '';

        if (empty($this->timestamps)) {

            $replace = infy_nl_tab() . "public \$timestamps = false;\n";
        }

        if ($this->commandData->getOption('fromTable') && !empty($this->timestamps)) {

            list($created_at, $updated_at, $deleted_at) = collect($this->timestamps)->map(function ($field) {

                return !empty($field) ? "'$field'" : 'null';
            });

            $replace .= infy_nl_tab() . "const CREATED_AT = $created_at;";
            $replace .= infy_nl_tab() . "const UPDATED_AT = $updated_at;";

            $softDelete = false;

            $deletedAtTimestamp = config('infyom.laravel_generator.timestamps.deleted_at', 'deleted_at');

            if ($this->commandData->getOption('softDelete')) {

                foreach ($this->commandData->fields as $field) {

                    if ($field->name == $deletedAtTimestamp) {

                        $softDelete = true;

                        break;
                    }
                }
            }

            if ($softDelete) {

                $replace .= infy_nl_tab() . "const DELETED_AT = $deleted_at;";
            }
        }

        return str_replace('$TIMESTAMPS$', $replace, $templateData);
    }

    private function fillSoftDeletes($templateData) {

        $softDeleteImport = $softDelete = $softDeleteDates = '';

        if ($this->timestamps) {

            $hasSoftDelete = $this->hasSoftDelete();

            if ($hasSoftDelete) {

                $softDeleteImport = "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n";
                $softDelete = infy_tab() . "use SoftDeletes;";
                $softDeleteDates = infy_nl_tab() . "protected \$dates = " . FormatHelper::writeValueToPhp($this->timestamps, 1) . ";\n";
            }
            else {

                $softDeleteDates = infy_nl_tab() . "protected \$dates = " . FormatHelper::writeValueToPhp([$this->timestamps[0], $this->timestamps[1]], 1) . ";\n";
            }
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
                "const CREATED_BY = $this->userStamps[0];",
                "const UPDATED_BY = $this->userStamps[1];"
            ];

            $hasSoftDelete = $this->hasSoftDelete();

            if ($hasSoftDelete) {

                $userStampsConstants[] = "const UPDATED_BY = $this->userStamps[2];"
            }

            $userStampsConstants = implode(infy_nl_tab(1, 1), $userStampsConstants);
        }

        $templateData = str_replace('$USER_STAMPS_IMPORT$', $userStampsImport, $templateData);
        $templateData = str_replace('$USER_STAMPS$', $userStamps, $templateData);
        $templateData = str_replace('$USER_STAMPS_CONSTANTS$', $userStampsDates, $templateData);

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
