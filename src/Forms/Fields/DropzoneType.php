<?php


namespace MediactiveDigital\MedKit\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class DropzoneType extends FormField {

    protected function getTemplate()
    {
        // At first it tries to load config variable,
        // and if fails falls back to loading view
        // resources/views/fields/datetime.blade.php
        return 'medKitTheme::forms.fields.dropzone';
    }

    public function setDefauts() {
        return [
            'url' => '/',
            'autoQueue' => false,
            'autoProcessQueue' => true,
            'addRemoveLinks' => true
        ];
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {

        // var_dump($options);
        // die();
        // if(isset($options['jsDropzoneOpts'])) {
        $options['jsDropzoneOpts'] =  $this->setDefauts();
        // }

        $options['attr']['class']='form-control';
        return parent::render($options, $showLabel, $showField, $showError);
    }
}
