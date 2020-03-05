<?php

namespace MediactiveDigital\MedKit\Generators;

use InfyOm\Generator\Generators\Scaffold\ControllerGenerator as InfyOmControllerGenerator;
use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Common\GeneratorField;
use InfyOm\Generator\Common\GeneratorFieldRelation;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Utils\TableFieldsGenerator;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;
use MediactiveDigital\MedKit\Helpers\Helper;

use Str;
use File;

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
    const DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER = 'TranslatableFkInteger';
    const DATATABLE_TYPE_ENUM = 'Enum';
    const DATATABLE_TYPE_CHOICE = 'Choice';
    const DATATABLE_TYPE_JSON = 'Json';
    const DATATABLE_TYPE_TRANSLATABLE = 'Translatable';

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
    private $helperPath;

    /** 
     * @var string 
     */
    private $dataTablePath;

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
    private $helperFileName;

    /** 
     * @var string 
     */
    private $dataTableFileName;

     /** 
     * @var string 
     */
    private $schemaPath;

    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path');
        $this->formPath = $this->commandData->config->pathForms;
        $this->helperPath = $this->commandData->config->pathHelpers;
        $this->dataTablePath = $this->commandData->config->pathDataTables;
        $this->fileName = $this->getReflectionProperty('fileName');
        $this->formFileName = $this->commandData->modelName . 'Form.php';
        $this->helperFileName = $this->commandData->modelName . 'Helper.php';
        $this->dataTableFileName = $this->commandData->modelName . 'DataTable.php';
        $this->schemaPath = $this->commandData->config->pathSchema;

        $this->setDefaults();
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
            $templateData = FormatHelper::cleanTemplate($templateData);

            FileUtil::createFile($this->formPath, $this->formFileName, $templateData);

            $this->commandData->commandComment("\nForm created: ");
            $this->commandData->commandInfo($this->formFileName);
        }
    }

    /** 
     * Generate helper
     *
     * @return void 
     */
    public function generateHelper() {

        $templateData = get_template('scaffold.helper.helper');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = FormatHelper::cleanTemplate($templateData);

        FileUtil::createFile($this->helperPath, $this->helperFileName, $templateData);

        $this->commandData->commandComment("\nHelper created: ");
        $this->commandData->commandInfo($this->helperFileName);
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
        $templateData = str_replace('$QUERY_JOINS$', $this->generateDataTableQueryJoins(), $templateData);
        $templateData = str_replace('$QUERY_SELECT$', $this->generateDataTableQuerySelect(), $templateData);
        $templateData = FormatHelper::cleanTemplate($templateData);

        FileUtil::createFile($this->dataTablePath, $this->dataTableFileName, $templateData);

        $this->commandData->commandComment("\nDataTable created: ");
        $this->commandData->commandInfo($this->dataTableFileName);
    }

    private function generateDataTableColumns() {

        $dataTableColumns = [];

        foreach ($this->commandData->formatedFields as $field) {

            if (!$field->inIndex) {

                continue;
            }

            $this->setDataTableMethods($field);
            $this->setDataTableAlias($field);
            $this->setDataTableJoin($field);
            $this->setDataTableFilter($field);

            $datas = [
                'title' => FormatHelper::UNESCAPE . '_i(' . FormatHelper::writeValueToPhp($this->getLabel($field->cleanName)) . ')'
            ];

            if (!$field->isSearchable) {

                $datas['searchable'] = false;
            }

            $dataTableColumns[$field->dataTableAlias] = FormatHelper::UNESCAPE . FormatHelper::writeValueToPhp($datas, 2, false, false);
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

        foreach ($this->commandData->formatedFields as $field) {

            $template = get_template('scaffold.datatable.edit_column' . ($field->dataTableType == self::DATATABLE_TYPE_TRANSLATABLE ? '_translatable' : ''));

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

    /** 
     * Generate datatable query joins
     *
     * @return string $queryJoins
     */
    private function generateDataTableQueryJoins(): string {

        $queryJoins = '';
        $template = get_template('scaffold.datatable.query_join');

        foreach ($this->commandData->formatedFields as $field) {

            if ($field->inIndex && in_array($field->dataTableType, [self::DATATABLE_TYPE_FK_INTEGER, self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER])) {

                $join = fill_template($this->commandData->dynamicVars, $template);
                $join = str_replace('$JOIN_TABLE_FULL_ALIAS$', $field->dataTableJoinTable == $field->dataTableJoinTableAlias ? $field->dataTableJoinTable : $field->dataTableJoinTable . ' AS ' . $field->dataTableJoinTableAlias, $join);
                $join = str_replace('$JOIN_TABLE_ALIAS$', $field->dataTableJoinTableAlias, $join);
                $join = str_replace('$JOIN_FIELD_NAME$', $field->dataTableJoinPrimaryField, $join);
                $join = str_replace('$FIELD_NAME$', $field->name, $join);

                $queryJoins .= $join;
            }
        }

        return $queryJoins;
    }

    /** 
     * Generate datatable query select
     *
     * @return string $querySelect
     */
    private function generateDataTableQuerySelect(): string {

        $querySelect = $selectFields = '';

        foreach ($this->commandData->formatedFields as $field) {

            if ($field->inIndex) {

                $value = '';

                switch ($field->dataTableType) {

                    case self::DATATABLE_TYPE_FK_INTEGER :
                    case self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER :

                        if ($field->dataTableJoinLabelField) {

                            $value = $field->dataTableType == self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER ? 
                                'DB::raw(TranslationHelper::getTranslatableQuery(\'' . $field->dataTableJoinLabelField . '\', \'' . $field->dataTableJoinTableAlias . '\') . \' AS ' . $field->dataTableAlias . '\')' : 
                                '\'' . $field->dataTableJoinTableAlias . '.' . $field->dataTableJoinLabelField . ' AS ' . $field->dataTableAlias . '\'';
                        }
                        else {

                            $rawQuery = 'NULL';

                            if ($field->dataTableJoinPrimaryField) {

                                $rawQuery = 'CONCAT(\\\'' . addcslashes(Str::ucfirst(str_replace('_', ' ', Str::singular(Str::lower($field->dataTableJoinTable)))), '\'') . ' ' . '\\\', `' . $field->dataTableJoinTableAlias . '`.`' . $field->dataTableJoinPrimaryField . '`)';
                            }

                            $value = 'DB::raw(\'' . $rawQuery . ' AS `' . $field->dataTableAlias . '`\')';
                        }

                    break;

                    case self::DATATABLE_TYPE_ENUM :
                    case self::DATATABLE_TYPE_CHOICE :

                        $rawQuery = 'NULL';

                        if ($field->htmlValues) {

                            $column = '`' . $this->commandData->dynamicVars['$TABLE_NAME$'] . '`.`' . $field->name . '`';

                            if ($field->dataTableType == self::DATATABLE_TYPE_CHOICE) {

                                $when = '';

                                foreach ($field->htmlValues as $htmlValue) {

                                    $htmlValue = explode(':', $htmlValue);

                                    $label = $htmlValue[0];
                                    $value = isset($htmlValue[1]) ? $htmlValue[1] : $label;

                                    $when .= ($when ? ' ' : '') . 'WHEN ' . $column . ' = \\\'' . addcslashes($value, '\'') . '\\\' THEN \\\'' . addcslashes($label, '\'') . '\\\'';
                                }

                                $rawQuery = 'CASE ' . $when . ' END';
                            }
                            else {

                                $list = '';

                                foreach ($field->htmlValues as $label) {

                                    $list .= ($list ? ', ' : '') . '\\\'' . addcslashes($label, '\'') . '\\\'';
                                }

                                $rawQuery = 'IF(' . $column . ' IN(' . $list . '), ' . $column . ', NULL)';
                            }
                        }

                        $value = 'DB::raw(\'' . $rawQuery . ' AS `' . $field->dataTableAlias . '`\')';

                    break;
                }

                $selectFields .= $value ? ',' . infy_nl_tab(1, 4) . $value : '';
            }
        }

        if ($selectFields) {

            $template = get_template('scaffold.datatable.query_select');

            $querySelect = fill_template($this->commandData->dynamicVars, $template);
            $querySelect = str_replace('$SELECT_FIELDS$', $selectFields, $querySelect);
        }

        return $querySelect;
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
				
				$templateData = str_replace('$AUTHORIZE_RESOURCE$', '$this->authorizeResource($MODEL_NAME$::class);', $templateData);
			} 
            else {
				
				$templateData = str_replace('$AUTHORIZE_RESOURCE$', '', $templateData);
			}
		}
		
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = FormatHelper::cleanTemplate($templateData);

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
                $sizeText = '';

                if (in_array($field->fieldType, ['enum', 'string', 'char', 'text', 'json'])) {

                    $sizeText = $validationText == 'min' ? 'minlength' : 'maxlength';
                }
                elseif ($field->htmlType == 'number') {

                    $sizeText = $validationText;
                }

                if ($sizeText) {

                    $attributes[$sizeText] = $sizeInNumber;
                }
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

        if ($field->htmlType == 'select' && isset($field->relation)) {

            $options['empty_value'] = FormatHelper::UNESCAPE . '_i(\'Sélectionnez\')';
            $options['choices'] = FormatHelper::UNESCAPE . '$this->getChoices(' . FormatHelper::writeValueToPhp(Str::snake(Str::plural($field->relation->inputs[0]))) . ')';
        }
        elseif ($field->htmlType == 'choice' && $field->htmlValues) {

            $options['expanded'] = true;
            $options['multiple'] = false;
            $options['choices'] = [];

            foreach ($field->htmlValues as $htmlValue) {

                $htmlValue = explode(':', $htmlValue);

                $label = $htmlValue[0];
                $value = isset($htmlValue[1]) ? $htmlValue[1] : $label;

                $options['choices'][$value] = $label;
            }
        }
        elseif ($field->htmlType == 'checkbox') {

            $options['value'] = 1;
        }
        elseif ($field->htmlType == 'datetime-local') {

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

        if ($field->dataTableType == self::DATATABLE_TYPE_JSON) {

            $options['value'] = FormatHelper::UNESCAPE . '$this->formatJson()';
        }

        if ($field->dataTableType == self::DATATABLE_TYPE_TRANSLATABLE) {

            $locales = config('laravel-gettext.supported-locales');
            $defaultLocale = config('laravel-gettext.locale');

            if (isset($options['attr']) && $options['attr']) {

                $attributes = $defaultLocaleAttributes = $options['attr'];

                unset($attributes['autofocus']);
                unset($attributes['required']);

                if ($attributes != $defaultLocaleAttributes) {

                    $options['attr'] = [];

                    foreach ($locales as $locale) {

                        if ($locale == $defaultLocale) {

                            $options[$locale]['attr'] = $defaultLocaleAttributes;
                        }
                        elseif ($attributes) {

                            $options[$locale]['attr'] = $attributes;
                        }
                    }
                }
            }
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
     * Get form fields as a string for the template
     *
     * @return string
     */
    public function getFormFields(): string {

        $fields = [];
        $first = false;
        
        foreach ($this->commandData->formatedFields as $field) {

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
     * Set fields defaults
     *
     * @return void
     */
    public function setDefaults() {

        foreach ($this->commandData->formatedFields as $field) {

            $this->setRealHtmlType($field);
            $this->setCleanFieldName($field);
            $this->setDataTableType($field);
        }
    }

    /** 
     * Set real HTML type defaults
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setRealHtmlType(GeneratorField $field) {

        if ($field->htmlType == 'number' && isset($field->relation)) {

            $field->htmlType = 'select';
        }
        elseif ($field->htmlType == 'radio' && $field->htmlValues) {

            $field->htmlType = 'choice';
        }
    }

    /** 
     * Set clean field name
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setCleanFieldName(GeneratorField $field) {

        $field->cleanName = $field->name;

        if ($field->htmlType == 'select' && isset($field->relation)) {

            $field->cleanName = Str::substr($field->name, 0, Str::length(Str::beforeLast(Str::lower($field->name), '_id')));
        }
    }

    /** 
     * Set field datatable type from HTML type
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setDataTableType(GeneratorField $field) {

        $field->dataTableType = null;

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

                if (in_array($field->fieldType, ['decimal', 'float'])) {

                    $field->dataTableType = self::DATATABLE_TYPE_FLOAT;
                }
                else {

                    $field->dataTableType = self::DATATABLE_TYPE_INTEGER;
                }

            break;

            case 'select' :

                $this->setFkDataTableType($field);

            break;

            case 'choice' :

                $associative = false;

                foreach ($field->htmlValues as $htmlValue) {

                    $htmlValue = explode(':', $htmlValue);

                    if (isset($htmlValue[1])) {

                        $associative = true;

                        break;
                    }
                }

                if ($associative) {

                    $field->dataTableType = self::DATATABLE_TYPE_CHOICE;
                }
                else {

                    $field->dataTableType = self::DATATABLE_TYPE_ENUM;
                }

            break;

            case 'text' :
            case 'textarea' :

                if (Helper::isJsonField($field)) {

                    $field->dataTableType = self::DATATABLE_TYPE_JSON;

                    if (Helper::isTranslatableField($field, $this->commandData)) {

                        $field->dataTableType = self::DATATABLE_TYPE_TRANSLATABLE;
                    }
                }

            break;
        }
    }

    /** 
     * Set field datatable type for HTML select type (foreign key)
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setFkDataTableType(GeneratorField $field) {

        $isTranslatable = false;

        if ($table = Helper::getTableName($field->cleanName)) {

            $tableFieldsGenerator = new TableFieldsGenerator($table, [], $this->commandData->config->connection);
            $tableFieldsGenerator->prepareRelations();
            $tableFieldsGenerator->prepareFieldsFromTable();

            foreach ($tableFieldsGenerator->fields as $relationField) {

                if (in_array($relationField->name, Helper::LABEL_FIELDS) && 
                    in_array($relationField->name, Helper::TRANSLATABLE_FIELDS) && 
                    Helper::isJsonField($relationField)) {

                    $isTranslatable = true;

                    break;
                }
            }
        }
        elseif ($class = Helper::getClassNameFromTableName($field->cleanName)) {

            if (($model = new $class) && $model->translatable) {

                foreach ($model->translatable as $translatable) {

                    if (in_array($translatable, Helper::LABEL_FIELDS) && 
                        in_array($translatable, Helper::TRANSLATABLE_FIELDS)) {

                        $isTranslatable = true;

                        break;
                    }
                }
            }
        }
        elseif (isset($field->relation->inputs[0]) && ($file = $field->relation->inputs[0])) {

            $path = config('infyom.laravel_generator.path.schema_files') . $file . '.json';

            if (File::exists($path) && ($json = json_decode(File::get($path), true))) {

                foreach ($json as $relationField) {

                    if (isset($relationField['name']) && 
                        in_array($relationField['name'], Helper::LABEL_FIELDS) && 
                        in_array($relationField['name'], Helper::TRANSLATABLE_FIELDS) && 
                        Helper::isJsonField($relationField)) {

                        $isTranslatable = true;

                        break;
                    }
                }
            }
        }

        $field->dataTableType = $isTranslatable ? self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER : self::DATATABLE_TYPE_FK_INTEGER;
    }

    /** 
     * Set field datatable methods from type
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setDataTableMethods(GeneratorField $field) {

        $field->dataTableMethods = self::DATATABLE_COLUMN_METHODS;

        switch ($field->dataTableType) {

            case self::DATATABLE_TYPE_JSON :
            case self::DATATABLE_TYPE_TRANSLATABLE :

                $field->dataTableMethods = [self::DATATABLE_COLUMN_EDIT];

            break;

            case self::DATATABLE_TYPE_FK_INTEGER :
            case self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER :
            case self::DATATABLE_TYPE_ENUM :
            case self::DATATABLE_TYPE_CHOICE :

                $field->dataTableMethods = [self::DATATABLE_COLUMN_FILTER];

            break;
        }
    }

    /** 
     * Set field datatable alias from type
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setDataTableAlias(GeneratorField $field) {

        $field->dataTableAlias = $field->name;

        switch ($field->dataTableType) {

            case self::DATATABLE_TYPE_FK_INTEGER :
            case self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER :

                $field->dataTableAlias = $field->cleanName;

            break;

            case self::DATATABLE_TYPE_ENUM :

                $field->dataTableAlias .= '_enum';

            break;

            case self::DATATABLE_TYPE_CHOICE :

                $field->dataTableAlias .= '_choice';

            break;
        }
    }

    /** 
     * Set field datatable join from type
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setDataTableJoin(GeneratorField $field) {

        $field->dataTableJoinTable = $field->dataTableJoinTableAlias = $field->dataTableJoinPrimaryField = $field->dataTableJoinLabelField = null;

        switch ($field->dataTableType) {

            case self::DATATABLE_TYPE_FK_INTEGER :
            case self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER :

                $field->dataTableJoinTable = Helper::getTableName($field->cleanName);
                $field->dataTableJoinTableAlias = $field->dataTableJoinTable . ($field->dataTableJoinTable == $this->commandData->dynamicVars['$TABLE_NAME$'] ? '_join' : '');
                $field->dataTableJoinPrimaryField = Helper::getTablePrimaryName($field->dataTableJoinTable);
                $field->dataTableJoinLabelField = Helper::getTableLabelName($field->dataTableJoinTable);

            break;
        }
    }

    /** 
     * Set field datatable filter from type
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return void
     */
    public function setDataTableFilter(GeneratorField $field) {

        $field->dataTableFilter = FormatHelper::writeValueToPhp($this->commandData->dynamicVars['$TABLE_NAME$'] . '.' . $field->name);

        switch ($field->dataTableType) {

            case self::DATATABLE_TYPE_FK_INTEGER :
            case self::DATATABLE_TYPE_TRANSLATABLE_FK_INTEGER :

                $field->dataTableFilter = FormatHelper::writeValueToPhp($field->dataTableJoinTableAlias);

            break;

            case self::DATATABLE_TYPE_ENUM :
            case self::DATATABLE_TYPE_CHOICE :

                $values = [];
                $associative = $field->dataTableType == self::DATATABLE_TYPE_CHOICE;

                foreach ($field->htmlValues as $htmlValue) {

                    if ($associative) {

                        $htmlValue = explode(':', $htmlValue);
                        $values[isset($htmlValue[1]) ? $htmlValue[1] : $htmlValue[0]] = $htmlValue[0];
                    }
                    else {

                        $values[] = $htmlValue;
                    }
                }

                $field->dataTableFilter .= $values ? ', ' . FormatHelper::writeValueToPhp($values, 0, false, false, $associative) : '';

            break;
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
                $fn = $field->dataTableType == self::DATATABLE_TYPE_TRANSLATABLE ? 'Translatable' : '';
                $name = $field->name . ($field->dataTableType == self::DATATABLE_TYPE_JSON ? '[value]' : '');

                $formFields[] = '$this->add' . $fn . '(\'' . $name . '\', ' . $type . ', ' . FormatHelper::writeValueToPhp($field->htmlOptions, 3) . ');';
            }
        }

        return implode(infy_nl_tab(2, 2), $formFields);
    }

    public function rollback() {

        if ($this->rollbackFile($this->formPath, $this->formFileName)) {

            $this->commandData->commandComment('Form file deleted: ' . $this->formFileName);
        }

        if ($this->rollbackFile($this->helperPath, $this->helperFileName)) {

            $this->commandData->commandComment('Helper file deleted: ' . $this->helperFileName);
        }

        if ($this->commandData->getAddOn('datatables')) {

            if ($this->rollbackFile($this->dataTablePath, $this->dataTableFileName)) {

                $this->commandData->commandComment('DataTable file deleted: ' . $this->dataTableFileName);
            }
        }

        if ($this->rollbackFile($this->path, $this->fileName)) {

            $this->commandData->commandComment('Controller file deleted: ' . $this->fileName);
        }
    }
}
