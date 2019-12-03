<?php

namespace MediactiveDigital\MedKit\Translations;

use Illuminate\Translation\Translator as IlluminateTranslator;

class Translator extends IlluminateTranslator {

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

        return $this->makeReplacements(_i($key), $replace);
    }
}
