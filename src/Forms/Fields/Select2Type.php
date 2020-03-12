<?php

namespace MediactiveDigital\MedKit\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

use Str;

class Select2Type extends FormField {

    /**
     * The name of the property that holds the value.
     *
     * @var string
     */
    protected $valueProperty = 'selected';

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    protected function getTemplate() {

        return 'medKitTheme::forms.fields.select2';
    }

    /**
     * Default options for field.
     *
     * @return array
     */
    protected function getDefaults() {

        return [
            'select2Opts' => [
                'closeOnSelect' => false,
                'multiple' => true
            ]
        ];
    }

    /**
     * Overload class attribute option for field.
     *
     * @return array
     */
    protected function getClassOverload() {

        return [
            'form-control',
            'js-select2'
        ];
    }

    /**
     * Render the field.
     *
     * @param array $options
     * @param bool $showLabel
     * @param bool $showField
     * @param bool $showError
     * @return string
     */
    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true) {

        if ($this->options['select2Opts']['multiple']) {

            $this->options['attr']['multiple'] = 'multiple';
        }
        else {

            $this->options['select2Opts']['closeOnSelect'] = true;
        }

        $this->options['attr']['id'] = isset($this->options['attr']['id']) ? $this->options['attr']['id'] : $this->name;
        $this->options['attr']['class'] = isset($this->options['attr']['class']) ? rtrim($this->options['attr']['class']) : '';

        foreach ($this->getClassOverload() as $class) {

            $this->options['attr']['class'] .= Str::contains($this->options['attr']['class'], $class) ? '' : ($this->options['attr']['class'] ? ' ' : '') . $class;
        }

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
