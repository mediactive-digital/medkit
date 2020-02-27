<?php

namespace MediactiveDigital\MedKit\Helpers;

use Carbon\Carbon;

use LaravelGettext;
use File;
use Str;
use Redirect;
use URL;

class TranslationHelper {

	/** 
     * Get datatable translations.
     *
     * @param string $locale
     * @return array $translations
     */
    public static function getDataTable(string $locale = ''): array {

        $translations = [];
        $language = ($language = FormatHelper::getLanguage($locale ?: LaravelGettext::getLocale())) ? str_replace(' ', '-', $language) : '';

        if ($language) {

            $path = base_path('node_modules/datatables.net-plugins/i18n');

            if (File::isDirectory($path)) {

                $files = File::files($path);

                foreach ($files as $file) {

                    $extension = $file->getExtension();
                    $name = $file->getBasename('.' . $extension);

                    if (Str::lower($language) == Str::lower($name)) {

                        $json = file_get_contents($path . '/' . $name . '.' . $extension);
                        $json = substr($json, strpos($json, '{'));
                        $json = json_decode(substr($json, 0, strrpos($json, '}') + 1), true);

                        $translations = $json ?: $translations;

                        break;
                    }
                }
            }
        }

        return $translations;
    }

    /**
     * Changes the current language and returns to previous page.
     *
     * @param string $locale
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function changeLang(string $locale = '') {

        $locale = $locale && in_array($locale, config('laravel-gettext.supported-locales')) ? $locale : LaravelGettext::getLocale();

        // Set locale and refresh locale file
        LaravelGettext::getTranslator()->setLocale($locale);
        Carbon::setLocale($locale);
        
        return Redirect::to(URL::previous());
    }

    /**
     * Get translatatable query.
     *
     * @param string $field
     * @param string $table
     * @param bool $unQuote
     *
     * @return string
     */
    public static function getTranslatableQuery(string $field = 'label', string $table = '', bool $unQuote = true): string {

        $locale = LaravelGettext::getLocale();
        $fallbackLocale = config('translatable.fallback_locale');
        $table .= $table ? '.' : '';
        $unQuote = $unQuote ? '>' : '';

        return 'IF(' . $table . $field . '->\'$.' . $locale . '\' != \'\' AND JSON_TYPE(' . $table . $field . '->\'$.' . $locale . '\') != \'NULL\', ' . 
            $table . $field . '->' . $unQuote . '\'$.' . $locale . '\', ' . $table . $field . '->' . $unQuote . '\'$.' . $fallbackLocale . '\')';
    }
}