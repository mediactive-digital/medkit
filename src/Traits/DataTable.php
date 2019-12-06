<?php

namespace MediactiveDigital\MedKit\Traits;

use MediactiveDigital\MedKit\Helpers\FormatHelper;

use LaravelGettext;
use Str;
use File;

trait DataTable {

    /** 
     * @var array $translations
     */
    private $translations;

    /** 
     * Get translations.
     *
     * @return array
     */
    private function getTranslations(): array {

        if ($this->translations === null) {

            $this->translations = [];
            $language = ($language = FormatHelper::getLanguage(LaravelGettext::getLocale())) ? str_replace(' ', '-', $language) : '';

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
                            $json = json_decode(substr($json, 0, strpos($json, '}') + 1), true);

                            $this->translations = $json ?: $this->translations;

                            break;
                        }
                    }
                }
            }
        }

        return $this->translations;
    }
}
