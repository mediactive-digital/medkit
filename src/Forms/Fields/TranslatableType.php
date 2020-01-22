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

        $name = $this->name;
        $type = $this->type;
        $template = $this->template;

        $this->type = $this->options['subtype'];
        $this->template = config('laravel-form-builder.' . $this->type);

        unset($this->options['subtype']);

        foreach ($locales as $locale) {

            $this->name = $name . '[' . $locale . ']';

            if ($model) {

                $this->options['value'] = $model->getTranslation($name, $locale);
            }

            $fields[$locale] = trim(parent::render([], false, true, false));
        }

        $this->name = $name;
        $this->type = $type;
        $this->template = $template;

        $this->options['value'] = $fields;

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
