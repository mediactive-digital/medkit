<?php

namespace MediactiveDigital\MedKit\Forms\Fields;

use MediactiveDigital\MedKit\Helpers\FormatHelper;

use Kris\LaravelFormBuilder\Fields\FormField;

use Str;
use Arr;
use LaravelGettext;

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
     * Default options for field.
     *
     * @return array
     */
    protected function getDefaults() {

        return [
            'ck_editor' => false,
            'ckEditorOpts' => [
                'minHeight' => '253px',
                'language' => LaravelGettext::getLocale()
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
            'js-translatable'
        ];
    }

    /**
     * get CK Editor toolbar option.
     *
     * @return array
     */
    protected function getCkEditorToolbarOption() {

        return [
            'heading', 
            '|', 
            'bold', 
            'italic', 
            'link', 
            'bulletedList', 
            'numberedList', 
            '|', 
            'blockQuote', 
            'insertTable', 
            'Undo', 
            'Redo'
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

        $locales = config('laravel-gettext.supported-locales');
        $model = $this->parent->getModel();
        $type = $this->options['subtype'] == 'textarea' ? $this->options['subtype'] : 'input';
        $ckEditor = $type == 'textarea' ? $this->options['ck_editor'] : null;
        $localesParameters = false;
        $fields = [];

        foreach ($this->options as $key => $value) {

            if (in_array($key, $locales) && is_array($value)) {

                $localesParameters = true;

                break;
            }
        }

        foreach ($locales as $locale) {

            $fields[$locale] = [];
            $value = $model ? $model->getTranslation($this->name, $locale, false) : null;
            $parameters = $localesParameters ? (isset($this->options[$locale]) && is_array($this->options[$locale]) ? $this->options[$locale] : []) : $this->options;
            $attributes = isset($parameters['attr']) ? $parameters['attr'] : [];
            $classes = isset($attributes['class']) ? rtrim($attributes['class']) : '';
            $flag = isset($parameters['flag']) && $parameters['flag'] ? FormatHelper::getFlag($parameters['flag']) : '';
            $flag = $flag ?: Arr::first(FormatHelper::getLocaleFlags($locale));

            foreach ($this->getClassOverload() as $class) {

                $classes .= Str::contains($classes, $class) ? '' : ($classes ? ' ' : '') . $class;
            }

            $fields[$locale]['button'] = [
                'type' => 'button',
                'attributes' => [
                    'type' => 'button'
                ],
                'value' => $flag ?: $locale
            ];

            if ($flag) {

                $fields[$locale]['button']['lang'] = FormatHelper::getLocaleTranslation($locale);
            }

            $fields[$locale]['field'] = [
                'type' => $type,
                'attributes' => array_merge($attributes, [
                    'class' => $classes,
                    'name' => $this->name . '[' . $locale . ']'
                ])
            ];

            if ($this->options['subtype'] == 'textarea') {

                $fields[$locale]['field']['value'] = $value;
                $fields[$locale]['field']['attributes']['cols'] = isset($fields[$locale]['field']['attributes']['cols']) ? $fields[$locale]['field']['attributes']['cols'] : 50;
                $fields[$locale]['field']['attributes']['rows'] = isset($fields[$locale]['field']['attributes']['rows']) ? $fields[$locale]['field']['attributes']['rows'] : 10;
                $fields[$locale]['field']['ck_editor'] = $ckEditor;

                if ($ckEditor) {

                    $fields[$locale]['field']['ckEditorOpts'] = isset($fields[$locale]['field']['ckEditorOpts']) ? $fields[$locale]['field']['ckEditorOpts'] : $this->options['ckEditorOpts'];
                    $fields[$locale]['field']['ckEditorOpts']['toolbar'] = isset($fields[$locale]['field']['ckEditorOpts']['toolbar']) ? $fields[$locale]['field']['ckEditorOpts']['toolbar'] : $this->getCkEditorToolbarOption();
                    $fields[$locale]['field']['attributes']['id'] = isset($fields[$locale]['field']['attributes']['id']) ? $fields[$locale]['field']['attributes']['id'] : $this->name . '-' . $locale;
                    $fields[$locale]['field']['attributes']['class'] .= ' js-ck-editor';
                }
            } else if($this->options['subtype'] == 'file' || $this->options['subtype'] == 'image' ) {

                $fields[$locale]['field']['attributes']['type']     = $this->options['subtype'];
                $fields[$locale]['field']['attributes']['data-val'] = $value;
            }
            else {

                $fields[$locale]['field']['attributes']['type'] = 'text';
                $fields[$locale]['field']['attributes']['value'] = $value;
            }
        }

        $this->options['value'] = $fields;

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
