<?php


namespace MediactiveDigital\MedKit\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

use Illuminate\Support\Str;

class DateTimePickerType extends FormField {

    protected function getTemplate()
    {
        // At first it tries to load config variable,
        // and if fails falls back to loading view
        // resources/views/fields/datetime.blade.php
        return 'medKitTheme::forms.fields.datetimepicker';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $this->options['attr']['data-target'] = '#datetimepicker-'.Str::slug( $this->options["real_name"], '-');
        $this->options['attr']['class'] = 'form-control datetimepicker-input';

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
