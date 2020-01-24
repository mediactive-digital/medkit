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
        $wrapper = $this->options['wrapper'];
        $label = $this->options['label'];
        $class = $this->options['attr']['class'];

        $buttonType = 'button';
        $buttonTemplate = config('laravel-form-builder.button');

        $fieldTemplate = config('laravel-form-builder.' . ($this->options['subtype'] == 'textarea' ? config('laravel-form-builder.default_translatable_textarea') : $this->options['subtype']));

        $this->options['wrapper'] = false;

        foreach ($locales as $locale) {

            $fields[$locale] = [];

            $this->type = $buttonType;
            $this->template = $buttonTemplate;
            $this->options['label'] = $locale;
            unset($this->options['attr']['class']);

            $fields[$locale]['button'] = trim(parent::render([], false, true, false));

            $this->name = $name . '[' . $locale . ']';

            if ($model) {

                $this->options['value'] = $model->getTranslation($name, $locale);
            }

            $this->type = $this->options['subtype'];
            $this->template = $fieldTemplate;
            $this->options['attr']['class'] = $class;

            $fields[$locale]['field'] = trim(parent::render([], false, true, false));
        }

        $this->name = $name;
        $this->type = $type;
        $this->template = $template;

        $this->options['wrapper'] = $wrapper;
        $this->options['label'] = $label;
        $this->options['value'] = $fields;

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
