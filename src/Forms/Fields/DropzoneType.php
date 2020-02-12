<?php


namespace MediactiveDigital\MedKit\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class DropzoneType extends FormField {

    protected function getTemplate()
    {
        // At first it tries to load config variable,
        // and if fails falls back to loading view
        // resources/views/fields/datetime.blade.php
        return 'dropzone';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['attr']['class']='dropzone';
        return parent::render($options, $showLabel, $showField, $showError);
    }
}