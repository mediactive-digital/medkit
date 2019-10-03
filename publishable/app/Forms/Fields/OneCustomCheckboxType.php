<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class OneCustomCheckboxType extends FormField {

    protected function getTemplate()
    {
        // At first it tries to load config variable,
        // and if fails falls back to loading view
        // resources/views/fields/datetime.blade.php
        return 'fields.onecustomcheckbox';
    }

    /**
     * @inheritdoc
     */
    public function getDefaults()
    {
        return [
            'type' => 'text',
            'attr' => ['class' => null, 'id' => $this->getName()],
            'value' => 1,
            'checked' => null
        ];
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        //$options['somedata'] = 'This is some data for view';

        return parent::render($options, $showLabel, $showField, $showError);
    }
}