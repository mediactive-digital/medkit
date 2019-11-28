<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use InfyOm\Generator\Generators\Scaffold\RequestGenerator as InfyOmRequestGenerator;
use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Utils\TableFieldsGenerator;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

use Illuminate\Validation\ValidationRuleParser;

use Str;

class RequestGenerator extends InfyOmRequestGenerator {

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
    private $baseFileName;

    /** 
     * @var string 
     */
    private $createFileName;

    /** 
     * @var string 
     */
    private $updateFileName;

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

    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path') . $this->commandData->modelName . '/';
        $this->baseFileName = 'Base' . $this->commandData->modelName . 'Request.php';
        $this->createFileName = $this->getReflectionProperty('createFileName');
        $this->updateFileName = $this->getReflectionProperty('updateFileName');
        $this->timestamps = TableFieldsGenerator::getTimestampFieldNames();
        $this->userStamps = TableFieldsGenerator::getUserStampsFieldNames();
        $this->lastActivity = TableFieldsGenerator::getLastActivityFieldName();
    }

    /** 
     * Generate base request
     *
     * @return void
     */
    private function generateBaseRequest() {

        $templateData = get_template('scaffold.request.base_request');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = str_replace('$RULES$', FormatHelper::writeValueToPhp($this->generateRules(), 2), $templateData);
        $templateData = str_replace('$MESSAGES$', FormatHelper::writeValueToPhp($this->generateMessages(), 2), $templateData);

        FileUtil::createFile($this->path, $this->baseFileName, $templateData);

        $this->commandData->commandComment("\nBase Request created: ");
        $this->commandData->commandInfo($this->baseFileName);
    }

    private function generateCreateRequest() {

        $templateData = get_template('scaffold.request.create_request');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, $this->createFileName, $templateData);

        $this->commandData->commandComment("\nCreate Request created: ");
        $this->commandData->commandInfo($this->createFileName);
    }

    private function generateUpdateRequest() {

        $templateData = get_template('scaffold.request.update_request');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, $this->updateFileName, $templateData);

        $this->commandData->commandComment("\nUpdate Request created: ");
        $this->commandData->commandInfo($this->updateFileName);
    }

    /**
     * Generate validation rules.
     *
     * @return array $rules
     */
    private function generateRules() {

        $dontRequireFields = config('infyom.laravel_generator.options.hidden_fields', []) + 
            config('infyom.laravel_generator.options.excluded_fields', []) +
            $this->timestamps + $this->userStamps + [$this->lastActivity];

        $rules = [];

        $class = '\\' . $this->commandData->config->nsModel . '\\' . $this->commandData->modelName;
        $model = new $class;
        $primaryKeyName = $model->getKeyName();
        $tableNameSingular = Str::singular($model->getTable());

        foreach ($this->commandData->fields as $field) {

            if (!$field->isPrimary && $field->isNotNull && empty($field->validations) && !in_array($field->name, $dontRequireFields)) {

                $field->validations = ['required'];
            }

            if (!empty($field->validations)) {

                // Move unique rule to last

                usort($field->validations, function($rule) {

                    return Str::startsWith($rule, 'unique:') ? 1 : 0;
                });

                $lastKey = count($field->validations) - 1;

                if ($lastKey >= 0 && Str::startsWith($field->validations[$lastKey], 'unique:')) {

                    $field->validations[$lastKey] = FormatHelper::writeValueToPhp($field->validations[$lastKey]);

                    $field->validations[$lastKey] = preg_replace_callback('/\$this->([a-zA-Z0-9]+)/', function($matches) use (&$primaryKeyName, &$tableNameSingular) {

                        return $matches[1] == $primaryKeyName ? '\' . $this->route(' . FormatHelper::writeValueToPhp($tableNameSingular) . ') . \'' : '\' . $this->' . $matches[1] . ' . \'';

                    }, $field->validations[$lastKey]);

                    $field->validations[$lastKey] = preg_replace('/\. \'\'$/', '', FormatHelper::UNESCAPE . $field->validations[$lastKey]);
                }

                $rules[$field->name] = $field->validations;
            }
        }

        return $rules;
    }

    /**
     * Generate validation messages.
     *
     * @return array $messages
     */
    private function generateMessages() {

        $messages = [];

        foreach ($this->commandData->fields as $field) {

            if ($field->validations) {

                foreach ($field->validations as $rule) {

                    $message = '';
                    [$formatedRule, $parameters] = ValidationRuleParser::parse($rule);

                    if ($formatedRule == 'Regex') {

                        if ($field->htmlType == 'password') {

                            if (isset($parameters[0]) && $parameters[0] == FormatHelper::PASSWORD_REGEX) {

                                $message = 'Le mot de passe doit contenir au minimum : une majuscule, une minuscule, un chiffre et un caractère spécial.';
                            }
                        }
                    }

                    if ($message) {

                        $messages[$field->name . '.' . Str::lower($formatedRule)] = FormatHelper::UNESCAPE . '_i(' . FormatHelper::writeValueToPhp($message) . ')';
                    }
                }
            }
        }

        ksort($messages);

        return $messages;
    }

    public function rollback() {

        if ($this->rollbackFile($this->path, $this->baseFileName)) {

            $this->commandData->commandComment('Base Request file deleted: ' . $this->baseFileName);
        }

        if ($this->rollbackFile($this->path, $this->createFileName)) {

            $this->commandData->commandComment('Create Request file deleted: ' . $this->createFileName);
        }

        if ($this->rollbackFile($this->path, $this->updateFileName)) {

            $this->commandData->commandComment('Update Request file deleted: ' . $this->updateFileName);
        }
    }
}
