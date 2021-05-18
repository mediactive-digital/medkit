<?php

namespace MediactiveDigital\MedKit\Translations;

use Illuminate\Translation\Translator as IlluminateTranslator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;

use MediactiveDigital\MedKit\Helpers\FormatHelper;

use Kris\LaravelFormBuilder\Fields\ChildFormType;
use Kris\LaravelFormBuilder\Fields\CollectionType;
use Kris\LaravelFormBuilder\Fields\ChoiceType;

use Arr;
use Str;

class Translator extends IlluminateTranslator {

    /** 
     * @var mixed $translatedForm 
     */
    private $translatedForm;

    /** 
     * @var array $poLaravel 
     */
    private $poLaravel;

    /**
     * Get the translation for the given key.
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @param bool $fallback
     * @return string|array
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true) {

        return $this->makeReplacements($this->getTranslations($key), $replace);
    }

    /**
     * Set current form for label translation.
     *
     * @return mixed 
     */
    private function setForm() {

        $request = request();
        $form = $request->translationForm ?: null;

        if (!$form) {

            $controller = $request->route()->controller;
            $form = $controller ? 'App\Forms\\' . str_replace('Controller', 'Form', Str::afterLast(get_class($controller), 'Controllers\\')) : $form;
        }

        if ($form && (!$this->translatedForm || get_class($this->translatedForm) != $form)) {

            if (class_exists($form)) {

                $model = $request->tableNameSingular ?: Str::snake(Str::afterLast(str_replace('Form', '', $form), '\\'));

                $this->translatedForm = app('laravel-form-builder')->create($form, [
                    'model' => $request->route($model),
                    'data' => [
                        'translation_form' => true
                    ]
                ]);

                $this->translatedForm->translatedFields = [];

                $fields = $this->getFormFields();

                foreach ($fields as $key => $field) {

                    $options = $field->getOptions();
                    $label = isset($options['first_options']['label']) && $options['first_options']['label'] ? $options['first_options']['label'] : (isset($options['label']) ? $options['label'] : '');

                    if ($label) {

                        $key = FormatHelper::transformToDotSyntax($key);

                        $keys = [
                            $key => $label
                        ];

                        if ($field->getType() == 'translatable') {

                            $keys = [];
                            $locales = config('laravel-gettext.supported-locales');

                            foreach ($locales as $locale) {

                                $keys[$key . '.' . $locale] = $label . ' ' . (FormatHelper::getLocaleTranslation($locale) ?: $locale);
                            }
                        }

                        foreach ($keys as $key => $label) {

                            $this->translatedForm->translatedFields[$key] = Str::lower($label);
                        }
                    }
                }
            }
            else {

                $this->translatedForm = null;
            }
        }

        return $this->translatedForm;
    }

    /**
     * Set default Laravel translations.
     *
     * @return array 
     */
    private function setTranslations(): array {

        $this->poLaravel = $this->poLaravel ?: (new FileLoader(new Filesystem, resource_path('lang')))->load('po_laravel', 'po_laravel');

        return $this->poLaravel;
    }

    /**
     * Get translations for the given key.
     *
     * @param string $key
     * @return string|array $translations
     */
    private function getTranslations(string $key) {

        $this->setTranslations();
        $translations = $this->poLaravel && ($translations = Arr::get($this->poLaravel, $key)) ? $translations : _i($key);

        if ($key == 'validation.attributes') {

            $translations = is_array($translations) ? $translations : [];

            $this->setForm();

            if ($this->translatedForm) {

                $translations = array_merge($translations, $this->translatedForm->translatedFields);
            }
        }

        return $translations;
    }

    /**
     * Get current form fields for label translation.
     *
     * @param \Kris\LaravelFormBuilder\Fields\ChildFormType|\Kris\LaravelFormBuilder\Fields\CollectionType|null $child
     *
     * @return array $fields
     */
    private function getFormFields($child = null): array {

        $fields = [];

        foreach ($child ? $child->getChildren() : $this->translatedForm->getFields() as $field) {

            if ($field instanceof ChildFormType || $field instanceof CollectionType || $field instanceof ChoiceType) {

                $fields += $this->getFormFields($field);
            }
            else {

                $name = $field->getName();
                
                if (Str::endsWith($name, '[]')) {

                    $arrayName = Str::beforeLast($name, '[');

                    $key = count(Arr::where($fields, function ($value, $key) use ($arrayName) {
                        
                        return Str::startsWith($key, $arrayName . '[');
                    }));

                    $name = $arrayName . '[' . $key . ']';
                }

                $fields[$name] = $field;
            }
        }

        return $fields;
    }
}
