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
     * @var string 
     */
    private $primaryKeyName;

    /** 
     * @var string 
     */
    private $tableNameSingular;

    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path');
        $this->fileName = $this->commandData->modelName . 'Request.php';
        $this->timestamps = TableFieldsGenerator::getTimestampFieldNames();
        $this->userStamps = TableFieldsGenerator::getUserStampsFieldNames();
        $this->lastActivity = TableFieldsGenerator::getLastActivityFieldName();

        $class = '\\' . $this->commandData->config->nsModel . '\\' . $this->commandData->modelName;
        $model = new $class;

        $this->primaryKeyName = $model->getKeyName();
        $this->tableNameSingular = Str::singular($model->getTable());

        $this->setRequestConfiguration();
    }

    /** 
     * Set configuration for request generation
     *
     * @return void 
     */
    private function setRequestConfiguration() {

        $this->commandData->addDynamicVariable('$TABLE_NAME_SINGULAR$', $this->tableNameSingular);
    }

    /** 
     * Generate request
     *
     * @return void
     */
    private function generateRequest() {

        $templateData = get_template('scaffold.request.request');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = str_replace('$RULES$', FormatHelper::writeValueToPhp($this->generateRules(), 2), $templateData);
        $templateData = str_replace('$MESSAGES$', FormatHelper::writeValueToPhp($this->generateMessages(), 2), $templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nBase Request created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    /**
     * Generate validation rules.
     *
     * @return array $rules
     */
    private function generateRules() {

        $dontRequireFields = array_merge(config('infyom.laravel_generator.options.hidden_fields', []), 
            config('infyom.laravel_generator.options.excluded_fields', []),
            $this->timestamps, $this->userStamps, [$this->lastActivity]);

        $rules = [];

        foreach ($this->commandData->fields as $field) {

            if (!$field->isPrimary && $field->isNotNull && !$field->validations && !in_array($field->name, $dontRequireFields)) {

                $field->validations = ['required'];
            }

            if ($field->validations) {

                // Move unique rule to last

                usort($field->validations, function($rule) {

                    return Str::startsWith($rule, 'unique:') ? 1 : 0;
                });

                $lastKey = count($field->validations) - 1;

                if ($lastKey >= 0 && Str::startsWith($field->validations[$lastKey], 'unique:')) {

                    $field->validations[$lastKey] = FormatHelper::writeValueToPhp($field->validations[$lastKey]);

                    $field->validations[$lastKey] = preg_replace_callback('/\$this->([a-zA-Z0-9_]+)/', function($matches) {

                        return $matches[1] == $this->primaryKeyName ? '\' . $this->modelId . \'' : '\' . $this->' . $matches[1] . ' . \'';

                    }, $field->validations[$lastKey]);

                    $field->validations[$lastKey] = preg_replace('/\. \'\'$/', '', FormatHelper::UNESCAPE . $field->validations[$lastKey]);
                }

                $rules[$field->name] = $field->validations;

                if (($key = array_search('required',  $field->validations)) !== false) {

                    if (Str::contains(Str::lower($field->name), 'password')) {

                        if (!in_array('nullable',  $field->validations)) {

                            $field->validations[] = 'nullable';
                            $rules[$field->name][$key] = FormatHelper::UNESCAPE . '$this->setRule(\'required\', \'nullable\')';
                        }
                    }
                }
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

        if ($this->rollbackFile($this->path, $this->fileName)) {

            $this->commandData->commandComment('Request file deleted: ' . $this->fileName);
        }
    }
}
