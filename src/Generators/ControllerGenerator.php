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

class ControllerGenerator extends InfyOmControllerGenerator {

    use Reflection;

    const DATATABLE_COLUMN_EDIT = 'edit';
    const DATATABLE_COLUMN_FILTER = 'filter';

    const DATATABLE_COLUMN_METHODS = [
        self::DATATABLE_COLUMN_EDIT,
        self::DATATABLE_COLUMN_FILTER
    ];

    const DATATABLE_TYPE_BOOLEAN = 'Boolean';
    const DATATABLE_TYPE_DATETIME = 'DateTime';
    const DATATABLE_TYPE_DATE = 'Date';
    const DATATABLE_TYPE_TIME = 'Time';
    const DATATABLE_TYPE_FLOAT = 'Float';
    const DATATABLE_TYPE_INTEGER = 'Integer';
    const DATATABLE_TYPE_FK_INTEGER = 'FkInteger';
    const DATATABLE_TYPE_ENUM = 'Enum';

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
        $this->path = $this->getReflectionProperty('path');
        $this->formPath = $this->commandData->config->pathForms;
        $this->fileName = $this->getReflectionProperty('fileName');
        $this->formFileName = $this->commandData->modelName . 'Form.php';
        $this->schemaPath = $this->commandData->config->pathSchema;
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
	 * Generate datatable
     *
     * @return void
	 */
    private function generateDataTable() { 
		
        $templateName = 'datatable';

		if (config('infyom.laravel_generator.add_on.permissions.enabled', true) && config('infyom.laravel_generator.add_on.permissions.policies', true)) {

			$templateName .= '_policies';
		}
		
        $templateData = get_template('scaffold.datatable.' . $templateName);
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = str_replace('$DATATABLE_COLUMNS$', FormatHelper::writeValueToPhp($this->generateDataTableColumns(), 2), $templateData);
        $templateData = str_replace('$EDIT_COLUMNS$', $this->generateDataTableEditColumns(), $templateData);
        $templateData = str_replace('$FILTER_COLUMNS$', $this->generateDataTableFilterColumns(), $templateData);

        $path = $this->commandData->config->pathDataTables;
        $fileName = $this->commandData->modelName . 'DataTable.php';

        FileUtil::createFile($path, $fileName, $templateData);

        $this->commandData->commandComment("\nDataTable created: ");
        $this->commandData->commandInfo($fileName);
    }

    private function generateDataTableColumns() {

        $dataTableColumns = [];

        foreach ($this->commandData->formatedFields as $field) {

            if (!$field->inIndex) {

                continue;
            }

            $this->getDataTableType($field);
            $this->getDataTableAlias($field);

            $datas = [
                'name' => $field->dataTableAlias,
                'data' => $field->dataTableAlias
            ];

            if (!$field->isSearchable) {

                $datas['searchable'] = false;
            }

            $dataTableColumns[FormatHelper::UNESCAPE . '_i(' . FormatHelper::writeValueToPhp($this->getLabel($field->cleanName)) . ')'] = $datas;
        }

        return $dataTableColumns;
    }

    /** 
     * Generate datatable column edition callbacks
     *
     * @return string $editColumns
     */
    private function generateDataTableEditColumns(): string {

        $editColumns = '';
        $template = get_template('scaffold.datatable.edit_column');

        foreach ($this->commandData->formatedFields as $field) {

            if ($field->inIndex && $field->dataTableType && in_array(self::DATATABLE_COLUMN_EDIT, $field->dataTableMethods)) {

                $editCallback = fill_template($this->commandData->dynamicVars, $template);
                $editCallback = str_replace('$FIELD_ALIAS$', $field->dataTableAlias, $editCallback);
                $editCallback = str_replace('$FIELD_TYPE$', $field->dataTableType, $editCallback);

                $editColumns .= $editCallback;
            }
        }

        return $editColumns;
    }

    /** 
     * Generate datatable column filter callbacks
     *
     * @return string $filterColumns
     */
    private function generateDataTableFilterColumns(): string {

        $filterColumns = '';
        $template = get_template('scaffold.datatable.filter_column');

        foreach ($this->commandData->formatedFields as $field) {

            if ($field->inIndex && $field->isSearchable && $field->dataTableType && in_array(self::DATATABLE_COLUMN_FILTER, $field->dataTableMethods)) {

                $filterCallback = fill_template($this->commandData->dynamicVars, $template);
                $filterCallback = str_replace('$FIELD_ALIAS$', $field->dataTableAlias, $filterCallback);
                $filterCallback = str_replace('$FIELD_TYPE$', $field->dataTableType, $filterCallback);
                $filterCallback = str_replace('$FIELD_FILTER$', $field->dataTableFilter, $filterCallback);

                $filterColumns .= $filterCallback;
            }
        }

        return $filterColumns;
    }

    public function generate() {

        if ($this->commandData->getAddOn('datatables')) {

            if ($this->commandData->getOption('repositoryPattern')) {

                $templateName = 'datatable_controller';
            } 
            else {

                $templateName = 'model_datatable_controller';
            }

            $templateData = get_template("scaffold.controller.$templateName");
        } 
        else {

            if ($this->commandData->getOption('repositoryPattern')) {

                $templateName = 'controller';
            } 
            else {

                $templateName = 'model_controller';
            }

            $templateData = get_template("scaffold.controller.$templateName");
            $paginate = $this->commandData->getOption('paginate');

            if ($paginate) {

                $templateData = str_replace('$RENDER_TYPE$', 'paginate(' . $paginate . ')', $templateData);
            } 
            else {

                $templateData = str_replace('$RENDER_TYPE$', 'all()', $templateData);
            }
        }

		if (in_array($templateName, ['controller' , 'datatable_controller'])) {
			
			if (config('infyom.laravel_generator.add_on.permissions.policies', true)) {
				
				$templateData = str_replace('$AUTHORIZE_RESOURCE$', '$this->authorizeResource(\$NAMESPACE_MODEL$\$MODEL_NAME$::class);', $templateData);
			} 
            else {
				
				$templateData = str_replace('$AUTHORIZE_RESOURCE$', '', $templateData);
			}
		}
		
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nController created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    /** 
     * Get a field html attributes
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return array $attributes 
     */
    public function getAttributes(GeneratorField $field): array {

        $attributes = [];

        if ($field->validations) {

            foreach ($field->validations as $validation) {

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
        }

        if (isset($field->autofocus)) {

            $attributes['autofocus'] = 'autofocus';
        }

        if ($field->htmlType == 'datetime-local') {

            $attributes['step'] = 1;
        }

        return $attributes;
    }

    /** 
     * Get a field html options
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return array $options 
     */
    public function getHtmlOptions(GeneratorField $field): array {

        $options = [
            'label' => FormatHelper::UNESCAPE . '_i(' . FormatHelper::writeValueToPhp($this->getLabel($field->cleanName)) . ')'
        ];

        if (($field->htmlType == 'number' || $field->htmlType == 'select') && isset($field->relation)) {

            $field->htmlType = 'select';
            $options['empty_value'] = FormatHelper::UNESCAPE . '_i(\'Sélectionnez\')';
            $options['choices'] = FormatHelper::UNESCAPE . '$this->getChoices(' . FormatHelper::writeValueToPhp(Str::snake(Str::plural($field->relation->inputs[0]))) . ')';
        }
        else if ($field->htmlType == 'checkbox' || $field->htmlType == 'radio' || $field->htmlType == 'choice') {

            if (($field->htmlType == 'radio' || $field->htmlType == 'choice') && $field->htmlValues) {

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
        else if ($field->htmlType == 'datetime-local') {

            $options['value'] = FormatHelper::UNESCAPE . '$this->formatDateTime()';
        }

        $attributes = $this->getAttributes($field);

        if ($attributes) {

            $options['attr'] = $attributes;
        }

        if ($field->htmlType == 'password') {

            if (isset($options['attr']['required'])) {

                $options['attr']['required'] = FormatHelper::UNESCAPE . '$this->setAttribute(\'required\', false)';
            }

            $optionsArray = $options;

            $options = [];
            $options['type'] = FormatHelper::UNESCAPE . 'Field::PASSWORD';
            $options['second_name'] = $field->name . '_confirmation';
            $options['second_options'] = $options['first_options'] = $optionsArray;
            $options['first_options']['value'] = FormatHelper::UNESCAPE . '$this->formatNull()';
            $options['second_options']['label'] = FormatHelper::UNESCAPE . '_i(' . FormatHelper::writeValueToPhp($this->getLabel($options['second_name'])) . ')';
        }

        return $options;
    }

    /** 
     * Get field label
     *
     * @param string $fieldName
     * @return string $label
     */
    public function getLabel(string $fieldName): string {

        $labelKey = 'validation.attributes.' . $fieldName;
        $label = !($label = _i($labelKey)) || $label == $labelKey ? $fieldName : $label;
        $label = Str::ucfirst(str_replace('_', ' ', Str::lower($label)));

        return $label;
    }

    /** 
     * Get field datatable type from HTML type
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function getDataTableType(GeneratorField $field) {

        $field->dataTableType = '';
        $field->dataTableMethods = self::DATATABLE_COLUMN_METHODS;

        switch ($field->htmlType) {

            case 'checkbox' :

                $field->dataTableType = self::DATATABLE_TYPE_BOOLEAN;

            break;

            case 'datetime-local' :

                $field->dataTableType = self::DATATABLE_TYPE_DATETIME;

            break;

            case 'date' :

                $field->dataTableType = self::DATATABLE_TYPE_DATE;

            break;

            case 'time' :

                $field->dataTableType = self::DATATABLE_TYPE_TIME;

            break;

            case 'number' :

                if (Str::startsWith($field->dbInput, 'decimal') || Str::startsWith($field->dbInput, 'float')) {

                    $field->dataTableType = self::DATATABLE_TYPE_FLOAT;
                }
                else {

                    $field->dataTableType = self::DATATABLE_TYPE_INTEGER;
                }

            break;

            case 'select' :

                $field->dataTableType = self::DATATABLE_TYPE_FK_INTEGER;
                $field->dataTableMethods = [self::DATATABLE_COLUMN_FILTER];

            break;

            case 'choice' :

                $field->dataTableType = self::DATATABLE_TYPE_ENUM;
                $field->dataTableMethods = [self::DATATABLE_COLUMN_FILTER];

            break;
        }
    }

    /** 
     * Get field datatable alias from type
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function getDataTableAlias(GeneratorField $field) {

        $field->dataTableAlias = $field->name;
        $field->dataTableFilter = $this->commandData->dynamicVars['$TABLE_NAME$'] . '.' . $field->name;

        switch ($field->dataTableType) {

            case self::DATATABLE_TYPE_FK_INTEGER :

                $field->dataTableAlias = $field->cleanName . '_label';
                $field->dataTableFilter = Str::snake(Str::plural($field->cleanName));

            break;

            case self::DATATABLE_TYPE_ENUM :

                $field->dataTableAlias .= '_choice';

            break;
        }
    }

    /** 
     * Get form fields as a string for the template
     *
     * @return string
     */
    public function getFormFields(): string {

        $fields = [];
        $first = false;
        
        foreach ($this->commandData->formatedFields as $field) {

            $this->setCleanFieldName($field);

            if ($field->inForm) {

                if (!$first) {

                    $field->autofocus = true;
                    
                    $first = true;
                }

                $field->htmlOptions = $this->getHtmlOptions($field);

                $fields[] = $field;
            }
        }

        return $this->prepareFormFields();
    }

    /** 
     * Set clean field name
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setCleanFieldName(GeneratorField $field) {

        $field->cleanName = $field->name;

        if (($field->htmlType == 'number' || $field->htmlType == 'select') && isset($field->relation)) {

            $field->cleanName = Str::beforeLast($field->name, '_id');
        }
    }

    /** 
     * Prepare form fields as a string for the template
     *
     * @return string
     */
    public function prepareFormFields(): string {

        $formFields = [];

        foreach ($this->commandData->formatedFields as $field) {

            if ($field->inForm) {

                $type = $field->htmlType == 'password' ? '\'repeated\'' : 'Field::' . strtoupper(str_replace('-', '_', $field->htmlType));

                $formFields[] = '$this->add(\'' . $field->name . '\', ' . $type . ', ' . FormatHelper::writeValueToPhp($field->htmlOptions, 3) . ');';
            }
        }

        return implode(infy_nl_tab(2, 2), $formFields);
    }

    public function rollback() {

        if ($this->rollbackFile($this->formPath, $this->formFileName)) {

            $this->commandData->commandComment('Form file deleted: ' . $this->formFileName);
        }

        if ($this->commandData->getAddOn('datatables')) {

            if ($this->rollbackFile($this->commandData->config->pathDataTables, $this->commandData->modelName . 'DataTable.php')) {

                $this->commandData->commandComment('DataTable file deleted: ' . $this->fileName);
            }
        }

        if ($this->rollbackFile($this->path, $this->fileName)) {

            $this->commandData->commandComment('Controller file deleted: ' . $this->fileName);
        }
    }
}
