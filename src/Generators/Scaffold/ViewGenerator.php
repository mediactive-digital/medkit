<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use Illuminate\Support\Arr;
use Illuminate\Support\Str; 
use InfyOm\Generator\Generators\BaseGenerator;
use InfyOm\Generator\Generators\ViewServiceProviderGenerator;
use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Utils\HTMLFieldGenerator;
 
use InfyOm\Generator\Generators\Scaffold\ViewGenerator  as InfyOmViewGenerator;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;

class ViewGenerator extends InfyOmViewGenerator    
{
    use Reflection;
	
	
	/// A surcharger !!!
	
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $templateType;

    /** @var array */
    private $htmlFields;
	 
	const TABLE_GENERATE_BLADE_FILE				 = 'table.blade.php';
	const INDEX_GENERATE_BLADE_FILE				 = 'index.blade.php';
	const FIELDS_GENERATE_BLADE_FILE			 = 'fields.blade.php';
	const CREATE_GENERATE_BLADE_FILE			 = 'create.blade.php';
	const EDIT_GENERATE_BLADE_FILE				 = 'edit.blade.php';
	const SHOW_GENERATE_BLADE_FILE				 = 'show.blade.php';
	const SHOW_FIELDS_GENERATE_BLADE_FILE		 = 'show_fields.blade.php';
	const DATATABLES_ACTIONS_GENERATE_BLADE_FILE = 'datatables_actions.blade.php';
	
    public function __construct(CommandData $commandData)   
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathViews; 
        $this->templateType = config('infyom.laravel_generator.templates', 'adminlte-templates');
    }
 
    public function generate()
    {
        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $htmlInputs = Arr::pluck($this->commandData->fields, 'htmlInput');
        if (in_array('file', $htmlInputs)) {
            $this->commandData->addDynamicVariable('$FILES$', ", 'files' => true");
        }

        $this->commandData->commandComment("\nGenerating Views...");

        if ($this->commandData->getOption('views')) {
            $viewsToBeGenerated = explode(',', $this->commandData->getOption('views'));

            if (in_array('index', $viewsToBeGenerated)) {
                $this->generateTable();
                $this->generateIndex();
            }

            if (count(array_intersect(['create', 'update'], $viewsToBeGenerated)) > 0) {
                $this->generateFields();
            }

            if (in_array('create', $viewsToBeGenerated)) {
                $this->generateCreate();
            }

            if (in_array('edit', $viewsToBeGenerated)) {
                $this->generateUpdate();
            }

            if (in_array('show', $viewsToBeGenerated)) {
                $this->generateShowFields();
                $this->generateShow();
            }
        } else {
            $this->generateTable();
            $this->generateIndex();
            // $this->generateFields();
            $this->generateCreate();
            $this->generateUpdate();
            $this->generateShowFields();
            $this->generateShow();
        }

        $this->commandData->commandComment('Views created: ');
    }

    private function generateTable()
    {
        if ($this->commandData->getAddOn('datatables')) {
            $templateData = $this->generateDataTableBody();
            $this->generateDataTableActions();
        } else {
            $templateData = $this->generateBladeTableBody();
        }

        FileUtil::createFile($this->path, self::TABLE_GENERATE_BLADE_FILE , $templateData);

        $this->commandData->commandInfo( self::TABLE_GENERATE_BLADE_FILE  . ' created');
    }

    private function generateDataTableBody()
    {
        $templateData = get_template('scaffold.views.datatable_body', $this->templateType);

        return fill_template($this->commandData->dynamicVars, $templateData);
    }

    private function generateDataTableActions()
    {
        $templateName = 'datatables_actions';

        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }

        $templateData = get_template('scaffold.views.'.$templateName, $this->templateType);

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, self::DATATABLES_ACTIONS_GENERATE_BLADE_FILE , $templateData);

        $this->commandData->commandInfo( self::DATATABLES_ACTIONS_GENERATE_BLADE_FILE . ' created');
    }
 
    private function generateIndex()
    {
        $templateName = 'index';

        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }

        $templateData = get_template('scaffold.views.'.$templateName, $this->templateType);

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        if ($this->commandData->getAddOn('datatables')) {
            $templateData = str_replace('$PAGINATE$', '', $templateData);
        } else {
            $paginate = $this->commandData->getOption('paginate');

            if ($paginate) {
                $paginateTemplate = get_template('scaffold.views.paginate', $this->templateType);

                $paginateTemplate = fill_template($this->commandData->dynamicVars, $paginateTemplate);

                $templateData = str_replace('$PAGINATE$', $paginateTemplate, $templateData);
            } else {
                $templateData = str_replace('$PAGINATE$', '', $templateData);
            }
        }

        FileUtil::createFile($this->path, self::INDEX_GENERATE_BLADE_FILE , $templateData);

        $this->commandData->commandInfo( self::INDEX_GENERATE_BLADE_FILE . ' created');
    }

    private function generateFields()
    {
        $templateName = 'fields';

        $localized = false;
        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
            $localized = true;
        }

        $this->htmlFields = [];

        foreach ($this->commandData->fields as $field) {
            if (!$field->inForm) {
                continue;
            }

            $validations = explode('|', $field->validations);
            $minMaxRules = '';
            foreach ($validations as $validation) {
                if (!Str::contains($validation, ['max:', 'min:'])) {
                    continue;
                }

                $validationText = substr($validation, 0, 3);
                $sizeInNumber = substr($validation, 4);

                $sizeText = ($validationText == 'min') ? 'minlength' : 'maxlength';
                if ($field->htmlType == 'number') {
                    $sizeText = $validationText;
                }

                $size = ",'$sizeText' => $sizeInNumber";
                $minMaxRules .= $size;
            }
            $this->commandData->addDynamicVariable('$SIZE$', $minMaxRules);

            $fieldTemplate = HTMLFieldGenerator::generateHTML($field, $this->templateType, $localized);

            if ($field->htmlType == 'selectTable') {
                $inputArr = explode(',', $field->htmlValues[1]);
                $columns = '';
                foreach ($inputArr as $item) {
                    $columns .= "'$item'".',';  //e.g 'email,id,'
                }
                $columns = substr_replace($columns, '', -1); // remove last ,

                $selectTable = $field->htmlValues[0];
                $tableName = $this->commandData->config->tableName;
                $variableName = Str::singular($selectTable).'Items'; // e.g $userItems

                $fieldTemplate = $this->generateViewComposer($tableName, $variableName, $columns, $selectTable);
            }

            if (!empty($fieldTemplate)) {
                $fieldTemplate = fill_template_with_field_data(
                    $this->commandData->dynamicVars,
                    $this->commandData->fieldNamesMapping,
                    $fieldTemplate,
                    $field
                );
                $this->htmlFields[] = $fieldTemplate;
            }
        }

        $templateData = get_template('scaffold.views.'.$templateName, $this->templateType);
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        $templateData = str_replace('$FIELDS$', implode("\n\n", $this->htmlFields), $templateData);

        FileUtil::createFile($this->path, self::FIELDS_GENERATE_BLADE_FILE , $templateData);
        $this->commandData->commandInfo( self::FIELDS_GENERATE_BLADE_FILE . ' created');
    }
 

    private function generateCreate()
    {
        $templateName = 'create';

        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }

        $templateData = get_template('scaffold.views.'.$templateName, $this->templateType);

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, self::CREATE_GENERATE_BLADE_FILE , $templateData);
        $this->commandData->commandInfo( self::CREATE_GENERATE_BLADE_FILE . ' created');
    }

    private function generateUpdate()
    {
        $templateName = 'edit';

        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }

        $templateData = get_template('scaffold.views.'.$templateName, $this->templateType);

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path,  self::EDIT_GENERATE_BLADE_FILE , $templateData);
        $this->commandData->commandInfo( self::EDIT_GENERATE_BLADE_FILE  . ' created');
    }

    private function generateShowFields()
    {
        $templateName = 'show_field';
        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }
        $fieldTemplate = get_template('scaffold.views.'.$templateName, $this->templateType);

        $fieldsStr = '';

        foreach ($this->commandData->fields as $field) {
            if (!$field->inView) {
                continue;
            }
            $singleFieldStr = str_replace('$FIELD_NAME_TITLE$', Str::title(str_replace('_', ' ', $field->name)),
                $fieldTemplate);
            $singleFieldStr = str_replace('$FIELD_NAME$', $field->name, $singleFieldStr);
            $singleFieldStr = fill_template($this->commandData->dynamicVars, $singleFieldStr);

            $fieldsStr .= $singleFieldStr."\n\n";
        }

        FileUtil::createFile($this->path, self::SHOW_FIELDS_GENERATE_BLADE_FILE, $fieldsStr);
        $this->commandData->commandInfo(  self::SHOW_FIELDS_GENERATE_BLADE_FILE . ' created');
    }

    private function generateShow()
    {
        $templateName = 'show';

        if ($this->commandData->isLocalizedTemplates()) {
            $templateName .= '_locale';
        }

        $templateData = get_template('scaffold.views.'.$templateName, $this->templateType);

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, self::SHOW_GENERATE_BLADE_FILE , $templateData);
        $this->commandData->commandInfo(  self::SHOW_GENERATE_BLADE_FILE . ' created');
    }

    public function rollback($views = [])
    {  
        $files = [
			self::TABLE_GENERATE_BLADE_FILE, 
			self::INDEX_GENERATE_BLADE_FILE,	 
			self::FIELDS_GENERATE_BLADE_FILE, 
			self::CREATE_GENERATE_BLADE_FILE, 
			self::EDIT_GENERATE_BLADE_FILE, 
			self::SHOW_GENERATE_BLADE_FILE, 
			self::SHOW_FIELDS_GENERATE_BLADE_FILE,  
        ];

        if (!empty($views)) {
            $files = [];
            foreach ($views as $view) {
                $files[] = $view.'.blade.php';
            }
        }

        if ($this->commandData->getAddOn('datatables')) {
            $files[] = self::DATATABLES_ACTIONS_GENERATE_BLADE_FILE;
        }

        foreach ($files as $file) {
            if ($this->rollbackFile($this->path, $file)) {
                $this->commandData->commandComment($file.' file deleted');
            }
        }
    }
}
