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
     * 
     * @return array $translations
     */
    public static function getDataTable(string $locale = ''): array {

        $translations = [];
        $locale = $locale ?: LaravelGettext::getLocale();

        if ($path = self::getPath($locale) ?? self::getPath($locale, true)) {

            $json = file_get_contents($path);
            $json = substr($json, strpos($json, '{'));
            $translations = json_decode(substr($json, 0, strrpos($json, '}') + 1), true) ?: $translations;
        }

        return $translations;
    }

    /** 
     * Get datatable translations path.
     *
     * @param string $locale
     * @param bool $first
     * 
     * @return string|null $filePath
     */
    public static function getPath(string $locale = '', bool $first = false): ?string {

        $filePath = null;
        $path = base_path('node_modules/datatables.net-plugins/i18n');

        if (File::isDirectory($path) && ($files = File::files($path))) {

            $locale = Str::lower($locale ?: LaravelGettext::getLocale());

            foreach ($files as $file) {

                $extension = $file->getExtension();

                if ($extension == 'json') {

                    $name = Str::lower($file->getBasename('.' . $extension));

                    if ($first ? Str::startsWith($name, $locale) : $locale == $name) {

                        $filePath = $file->getPathName();

                        break;
                    }
                }
            }
        }

        return $filePath;
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
