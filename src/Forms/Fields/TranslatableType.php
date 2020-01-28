<?php

namespace MediactiveDigital\MedKit\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class TranslatableType extends FormField {

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    protected function getTemplate() {

        return 'translatable';
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

        $locales = config('laravel-gettext.supported-locales');
        $model = $this->parent->getModel();
        $fields = [];

        foreach ($locales as $locale) {

            $fields[$locale] = [];
            $value = $model ? $model->getTranslation($this->name, $locale) : null;

            $fields[$locale]['button'] = [
                'type' => 'button',
                'attributes' => [
                    'type' => 'button'
                ],
                'value' => $locale
            ];

            $fields[$locale]['field'] = [
                'type' => $this->options['subtype'] == 'textarea' ? $this->options['subtype'] : 'input',
                'attributes' => [
                    'class' => 'form-control',
                    'name' => $this->name . '[' . $locale . ']'
                ]
            ];

            if ($this->options['subtype'] == 'textarea') {

                $fields[$locale]['field']['value'] = $value;
                $fields[$locale]['field']['attributes']['cols'] = 50;
                $fields[$locale]['field']['attributes']['rows'] = 10;
            }
            else {

                $fields[$locale]['field']['attributes']['type'] = 'text';
                $fields[$locale]['field']['attributes']['value'] = $value;
            }
        }

        unset($this->options['subtype']);

        $this->options['value'] = $fields;

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
