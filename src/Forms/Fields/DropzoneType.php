<?php

namespace MediactiveDigital\MedKit\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

use Str;

class DropzoneType extends FormField {

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    protected function getTemplate() {

        return 'medKitTheme::forms.fields.dropzone';
    }

    /**
     * Default options for field.
     *
     * @return array
     */
	protected function getDefaults() {

        return [
            'jsDropzoneOpts' => [
                'url' => '/',
                'autoQueue' => false,
                'addRemoveLinks' => true
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
            'js-dropzone'
        ];
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true) {

        $this->options['attr']['id'] = isset($this->options['attr']['id']) ? $this->options['attr']['id'] : $this->name;
        $this->options['attr']['class'] = isset($this->options['attr']['class']) ? rtrim($this->options['attr']['class']) : '';

        foreach ($this->getClassOverload() as $class) {

            $this->options['attr']['class'] .= Str::contains($this->options['attr']['class'], $class) ? '' : ($this->options['attr']['class'] ? ' ' : '') . $class;
        }

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
