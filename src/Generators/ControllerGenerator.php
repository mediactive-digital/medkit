<?php

namespace MediactiveDigital\MedKit\Generators;

use InfyOm\Generator\Generators\Scaffold\ControllerGenerator as InfyOmControllerGenerator;
use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Common\GeneratorField;
use InfyOm\Generator\Common\GeneratorFieldRelation;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

use Str;
use DB;

class ControllerGenerator extends InfyOmControllerGenerator {

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
    private $formPath;

    /** 
     * @var string 
     */
    private $fileName;

    /** 
     * @var string 
     */
    private $formFileName;

     /** 
     * @var string 
     */
    private $schemaPath;

    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;

        $this->setFormConfiguration();

        $this->path = $this->getReflectionProperty('path');
        $this->formPath = $this->commandData->config->pathForms;
        $this->fileName = $this->getReflectionProperty('path');
        $this->formFileName = $this->commandData->modelName . 'Form.php';
        $this->schemaPath = config('infyom.laravel_generator.path.schema_files', resource_path('model_schemas/'));
    }

    /** 
     * Set configuration for form generation
     *
     * @return void 
     */
    private function setFormConfiguration() {

        $prefix = $this->commandData->getNameSpacePrefix();

        $this->commandData->addDynamicVariable('$NAMESPACE_FORMS$', config('infyom.laravel_generator.namespace.forms', 'App\Forms') . $prefix);
        $this->commandData->config->addOns['forms'] = config('infyom.laravel_generator.add_on.forms', true);
        $this->commandData->config->pathForms = config('infyom.laravel_generator.path.forms', app_path('Forms/')) . $prefix;
    }

    /** 
     * Generate form
     *
     * @return void 
     */
    public function generateForm() {

        if ($this->commandData->getAddOn('forms')) {

            $this->commandData->addDynamicVariable('$FORM_FIELDS$', $this->getFormFields());

            $templateData = get_template('scaffold.form.form');
            $templateData = fill_template($this->commandData->dynamicVars, $templateData);

            FileUtil::createFile($this->formPath, $this->formFileName, $templateData);

            $this->commandData->commandComment("\nForm created: ");
            $this->commandData->commandInfo($this->formFileName);
        }
    }

    /** 
     * Get form fields from JSON schema file
     *
     * @return array $fields 
     */
    public function getJsonFields() {

        $json = file_get_contents($this->schemaPath . $this->commandData->config->mName . '.json');
        $datas = json_decode($json, true);
        $fields = $relations = [];

        foreach ($datas as $data) {

            if (isset($data['type'])) {

                if ($data['relation']) {

                    $relation = GeneratorFieldRelation::parseRelation($data['relation']);

                    if ($relation->type == 'mt1') {

                        $relations[$relation->inputs[1]] = $relation;
                    }
                }
            } 
            else if ($data['inForm']) {

                $fields[] = GeneratorField::parseFieldFromFile($data);
            }
        }

        foreach ($fields as $field) {

            if (isset($relations[$field->name])) {

                $field->relation = $relations[$field->name];
            }
        }

        return $fields;
    }

    /** 
     * Get select choices for a field that has a relation
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return array $choices 
     */
    public function getRelationChoices($field) {

        $choices = [];

        $relationJson = file_get_contents($this->schemaPath . $field->relation->inputs[0] . '.json');
        $relationDatas = json_decode($relationJson, true);

        $colId = $colName = $nom = $name = $libelle = $label = null;

        foreach ($relationDatas as $relationData) {

            if (!isset($relationData['type'])) {

                $relationData = GeneratorField::parseFieldFromFile($relationData);

                if ($relationData->isPrimary) {

                    $colId = $relationData->name;
                }
                else if (!$colName) {

                    switch ($relationData->name) {

                        case 'nom' :

                            $nom = $relationData->name;

                        break;

                        case 'name' :

                            $name = $relationData->name;

                        break;

                        case 'libelle' :

                            $libelle = $relationData->name;

                        break;

                        case 'label' :

                            $label = $relationData->name;

                        break;
                    }
                }
            }
        }

        $colName = $nom ?: ($name ?: ($libelle ?: ($label ?: $colName)));

        if ($colId && $colName) {

            $class = '\\' . $this->commandData->config->nsModel . '\\' . $field->relation->inputs[0];
            $table = (new $class)->getTable();

            $relations = DB::table($table)->select([$colId, $colName])->orderBy($colName)->limit(100)->get();

            if ($relations) {

                $choices = $relations->pluck($colName, $colId)->toArray();
            }
        }

        return $choices;
    }

    /** 
     * Get a field html attributes
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return array $attributes 
     */
    public function getAttributes($field) {

        $attributes = [];
        $validations = explode('|', $field->validations);

        foreach ($validations as $validation) {

            if ($validation == 'required') {

                $attributes[$validation] = $validation;

                continue;
            }

            if (!Str::contains($validation, ['max:', 'min:', 'required'])) {

                continue;
            }

            $validationText = substr($validation, 0, 3);
            $sizeInNumber = substr($validation, 4);

            $sizeText = $validationText == 'min' ? 'minlength' : 'maxlength';

            if ($field->htmlType == 'number') {

                $sizeText = $validationText;
            }

            $attributes[$sizeText] = $sizeInNumber;
        }

        if (isset($field->autofocus)) {

            $attributes['autofocus'] = 'autofocus';
        }

        return $attributes;
    }

    /** 
     * Get a field html options
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return array $options 
     */
    public function getHtmlOptions($field) {

        $options = [
            'label' => $field->name
        ];

        if ($field->htmlType == 'number' && isset($field->relation)) {

            $field->htmlType = 'select';
            $options['empty_value'] = _i('Sélectionnez');
            $choices = $this->getRelationChoices($field);

            if ($choices) {

                $options['choices'] = $choices;
            }
        }
        else if ($field->htmlType == 'checkbox' || $field->htmlType == 'radio') {

            if ($field->htmlType == 'radio' && $field->htmlValues) {

                $field->htmlType = 'choice';
                $options['expanded'] = true;
                $options['multiple'] = false;
                $options['choices'] = [];

                foreach ($field->htmlValues as $htmlValue => $htmlName) {

                    $options['choices'][$htmlValue] = $htmlName;
                }
            }
            else {

                $options['value'] = 1;
            }
        }

        $attributes = $this->getAttributes($field);

        if ($attributes) {

            $options['attr'] = $attributes;
        }

        return $options;
    }

    /** 
     * Get form fields as a string for the template
     *
     * @return string
     */
    public function getFormFields() {

        $fields = $this->getJsonFields();
        
        foreach ($fields as $key => $field) {

            if ($key == 0) {

                $field->autofocus = true;
            }

            $field->htmlOptions = $this->getHtmlOptions($field);
        }

        return $this->prepareFormFields($fields);
    }

    /** 
     * Prepare form fields as a string for the template
     *
     * @param array $fields
     * @return string
     */
    public function prepareFormFields(array $fields) {

        $formFields = [];

        foreach ($fields as $field) {

            $formFields[] = '$this->add(\'' . $field->name . '\', Field::' . strtoupper(str_replace('-', '_', $field->htmlType)) . ', ' . FormatHelper::writeValueToPhp($field->htmlOptions, 3) . ');';
        }

        return implode(infy_nl_tab(2, 2), $formFields);
    }

    private function generateDataTableColumns() {

        $headerFieldTemplate = get_template('scaffold.views.datatable_column', 'templates');

        $dataTableColumns = [];

        foreach ($this->commandData->fields as $field) {

            if (!$field->inIndex) {

                continue;
            }

            $fieldTemplate = fill_template_with_field_data($this->commandData->dynamicVars, $this->commandData->fieldNamesMapping, $headerFieldTemplate, $field);

            if ($field->isSearchable) {

                $dataTableColumns[] = $fieldTemplate;
            } 
            else {

                $dataTableColumns[] = "'" . $field->name . "' => ['searchable' => false]";
            }
        }

        return $dataTableColumns;
    }

    public function rollback() {

        if ($this->rollbackFile($this->formPath, $this->formFileName)) {

            $this->commandData->commandComment('Form file deleted: ' . $this->formFileName);
        }

        if ($this->rollbackFile($this->path, $this->fileName)) {

            $this->commandData->commandComment('Controller file deleted: ' . $this->fileName);
        }

        if ($this->commandData->getAddOn('datatables')) {

            if ($this->rollbackFile($this->commandData->config->pathDataTables, $this->commandData->modelName . 'DataTable.php')) {

                $this->commandData->commandComment('DataTable file deleted: ' . $this->fileName);
            }
        }
    }
}
