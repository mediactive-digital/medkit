<?php

namespace MediactiveDigital\MedKit\Helpers;

use LaravelGettext;
use File;
use Str;

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
}