<?php

namespace MediactiveDigital\MedKit\Helpers;

use Carbon\Carbon;

use Str;
use LaravelGettext;

class FormatHelper {

    const START_YEAR = 2017;

    const UNESCAPE = '##UNESCAPE_PATTERN##';
    const TAB = '    ';
    const NEW_LINE = "\n";

    const PASSWORD_REGEX = '/^.*(?=.{8,120})(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])(?=.*[^a-zA-Z\d\s]).*$/';

    const LANGUAGES = [
        'aa' => 'Afar',
        'ab' => 'Abkhazian',
        'ae' => 'Avestan',
        'af' => 'Afrikaans',
        'ak' => 'Akan',
        'am' => 'Amharic',
        'an' => 'Aragonese',
        'ar' => 'Arabic',
        'as' => 'Assamese',
        'av' => 'Avaric',
        'ay' => 'Aymara',
        'az' => 'Azerbaijani',
        'ba' => 'Bashkir',
        'be' => 'Belarusian',
        'bg' => 'Bulgarian',
        'bh' => 'Bihari',
        'bi' => 'Bislama',
        'bm' => 'Bambara',
        'bn' => 'Bengali',
        'bo' => 'Tibetan',
        'br' => 'Breton',
        'bs' => 'Bosnian',
        'ca' => 'Catalan',
        'ce' => 'Chechen',
        'ch' => 'Chamorro',
        'co' => 'Corsican',
        'cr' => 'Cree',
        'cs' => 'Czech',
        'cu' => 'Church Slavic',
        'cv' => 'Chuvash',
        'cy' => 'Welsh',
        'da' => 'Danish',
        'de' => 'German',
        'dv' => 'Divehi',
        'dz' => 'Dzongkha',
        'ee' => 'Ewe',
        'el' => 'Greek',
        'en' => 'English',
        'eo' => 'Esperanto',
        'es' => 'Spanish',
        'et' => 'Estonian',
        'eu' => 'Basque',
        'fa' => 'Persian',
        'ff' => 'Fulah',
        'fi' => 'Finnish',
        'fj' => 'Fijian',
        'fo' => 'Faroese',
        'fr' => 'French',
        'fy' => 'Western Frisian',
        'ga' => 'Irish',
        'gd' => 'Scottish Gaelic',
        'gl' => 'Galician',
        'gn' => 'Guarani',
        'gu' => 'Gujarati',
        'gv' => 'Manx',
        'ha' => 'Hausa',
        'he' => 'Hebrew',
        'hi' => 'Hindi',
        'ho' => 'Hiri Motu',
        'hr' => 'Croatian',
        'ht' => 'Haitian',
        'hu' => 'Hungarian',
        'hy' => 'Armenian',
        'hz' => 'Herero',
        'ia' => 'Interlingua',
        'id' => 'Indonesian',
        'ie' => 'Interlingue',
        'ig' => 'Igbo',
        'ii' => 'Sichuan Yi',
        'ik' => 'Inupiaq',
        'io' => 'Ido',
        'is' => 'Icelandic',
        'it' => 'Italian',
        'iu' => 'Inuktitut',
        'ja' => 'Japanese',
        'jv' => 'Javanese',
        'ka' => 'Georgian',
        'kg' => 'Kongo',
        'ki' => 'Kikuyu',
        'kj' => 'Kwanyama',
        'kk' => 'Kazakh',
        'kl' => 'Kalaallisut',
        'km' => 'Khmer',
        'kn' => 'Kannada',
        'ko' => 'Korean',
        'kr' => 'Kanuri',
        'ks' => 'Kashmiri',
        'ku' => 'Kurdish',
        'kv' => 'Komi',
        'kw' => 'Cornish',
        'ky' => 'Kirghiz',
        'la' => 'Latin',
        'lb' => 'Luxembourgish',
        'lg' => 'Ganda',
        'li' => 'Limburgish',
        'ln' => 'Lingala',
        'lo' => 'Lao',
        'lt' => 'Lithuanian',
        'lu' => 'Luba-Katanga',
        'lv' => 'Latvian',
        'mg' => 'Malagasy',
        'mh' => 'Marshallese',
        'mi' => 'Maori',
        'mk' => 'Macedonian',
        'ml' => 'Malayalam',
        'mn' => 'Mongolian',
        'mr' => 'Marathi',
        'ms' => 'Malay',
        'mt' => 'Maltese',
        'my' => 'Burmese',
        'na' => 'Nauru',
        'nb' => 'Norwegian Bokmal',
        'nd' => 'North Ndebele',
        'ne' => 'Nepali',
        'ng' => 'Ndonga',
        'nl' => 'Dutch',
        'nn' => 'Norwegian Nynorsk',
        'no' => 'Norwegian',
        'nr' => 'South Ndebele',
        'nv' => 'Navajo',
        'ny' => 'Chichewa',
        'oc' => 'Occitan',
        'oj' => 'Ojibwa',
        'om' => 'Oromo',
        'or' => 'Oriya',
        'os' => 'Ossetian',
        'pa' => 'Panjabi',
        'pi' => 'Pali',
        'pl' => 'Polish',
        'ps' => 'Pashto',
        'pt' => 'Portuguese',
        'qu' => 'Quechua',
        'rm' => 'Raeto-Romance',
        'rn' => 'Kirundi',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'rw' => 'Kinyarwanda',
        'sa' => 'Sanskrit',
        'sc' => 'Sardinian',
        'sd' => 'Sindhi',
        'se' => 'Northern Sami',
        'sg' => 'Sango',
        'si' => 'Sinhala',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'sm' => 'Samoan',
        'sn' => 'Shona',
        'so' => 'Somali',
        'sq' => 'Albanian',
        'sr' => 'Serbian',
        'ss' => 'Swati',
        'st' => 'Southern Sotho',
        'su' => 'Sundanese',
        'sv' => 'Swedish',
        'sw' => 'Swahili',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'tg' => 'Tajik',
        'th' => 'Thai',
        'ti' => 'Tigrinya',
        'tk' => 'Turkmen',
        'tl' => 'Tagalog',
        'tn' => 'Tswana',
        'to' => 'Tonga',
        'tr' => 'Turkish',
        'ts' => 'Tsonga',
        'tt' => 'Tatar',
        'tw' => 'Twi',
        'ty' => 'Tahitian',
        'ug' => 'Uighur',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'uz' => 'Uzbek',
        've' => 'Venda',
        'vi' => 'Vietnamese',
        'vo' => 'Volapuk',
        'wa' => 'Walloon',
        'wo' => 'Wolof',
        'xh' => 'Xhosa',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'za' => 'Zhuang',
        'zh' => 'Chinese',
        'zu' => 'Zulu'
    ];

    /**
     * Retourne la liste des années depuis l'année de départ jusqu'à l'année en cours
     *
     * @param mixed $endYear
     * @return array $years
     */
    public static function getYears($endYear = null) {

        $years = [];
        $endYear = $endYear === null ? Carbon::now()->year : ((int)$endYear > self::START_YEAR ? (int)$endYear : self::START_YEAR);

        for ($year = self::START_YEAR; $year <= $endYear; $year++) {

            $years[$year] = $year;
        }

        return $years;
    }

    /**
     * Retourne la liste des mois de l'année
     *
     * @param mixed $monthIds
     * @return mixed array|string $months
     */
    public static function getMonths($monthIds = null) {

        $months = [];
        $returnAsArray = true;

        if ($monthIds === null) {

            $monthIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        }
        elseif (!is_array($monthIds)) {

            $monthIds = [$monthIds];
            $returnAsArray = false;
        }

        foreach ($monthIds as $monthId) {

            switch ($monthId) {

                case 1 :

                    $months[1] = _i('Janvier');

                break;

                case 2 :

                    $months[2] = _i('Février');

                break;

                case 3 :

                    $months[3] = _i('Mars');

                break;

                case 4 :

                    $months[4] = _i('Avril');

                break;

                case 5 :

                    $months[5] = _i('Mai');

                break;

                case 6 :

                    $months[6] = _i('Juin');

                break;

                case 7 :

                    $months[7] = _i('Juillet');

                break;

                case 8 :

                    $months[8] = _i('Août');

                break;

                case 9 :

                    $months[9] = _i('Septembre');

                break;

                case 10 :

                    $months[10] = _i('Octobre');

                break;

                case 11 :

                    $months[11] = _i('Novembre');

                break;

                case 12 :

                    $months[12] = _i('Décembre');

                break;
            }
        }

        if (!$returnAsArray) {

            $months = count($months) == 1 ? reset($months) : '';
        }

        return $months;
    }

    /**
     * Convertit une valeur en octets (php.ini)
     *
     * @param mixed string|int|float $value
     * @return mixed int|float $value
     */
    public static function convertToBytes($value) {

        $value = trim($value);
        $unit = strtolower($value[strlen($value) - 1]);
        $value = (float)$value;

        switch ($unit) {

            case 'g':

                $value *= 1024;

            case 'm':

                $value *= 1024;

            case 'k':

                $value *= 1024;
        }

        return (int)$value == $value ? (int)$value : $value;
    }

    /**
     * Retourne la taille maximum autorisée pour l'upload des fichiers
     *
     * @param string $format
     * @return mixed int|float $maxSize
     */
    public static function getUploadMaxSize(string $format = 'Mo') {

        $format = strtolower($format);
        $uploadMaxFilesize = self::convertToBytes(ini_get('upload_max_filesize'));
        $postMaxSize = self::convertToBytes(ini_get('post_max_size'));
        $memoryLimit = self::convertToBytes(ini_get('memory_limit'));
        $maxSize = min($uploadMaxFilesize, $postMaxSize, $memoryLimit);

        switch ($format) {

            case 'o' :

            break;

            case 'go' :

                $maxSize /= 1024;

            case 'mo' :
            default:

                $maxSize /= 1024;

            case 'ko' :

                $maxSize /= 1024;
        }

        return (int)$maxSize == $maxSize ? (int)$maxSize : $maxSize;
    }

    /**
     * Retire les espaces d'une chaîne de caractères
     *
     * @param mixed $value
     * @return mixed $value
     */
    public static function removeSpaces($value) {

        return $value && is_string($value) ? preg_replace('/\s+/', '', $value) : $value;
    }

    /**
     * Transform PHP Array to Config string array
     *
     * @param array $array
     * @param int $level 
     * @param bool $doubleQuotes
     * @param bool $newLines
     * @param bool|null $associative
     * @return string $string
     */
    public static function writeArrayToPhp(array $array, int $level = 0, bool $doubleQuotes = false, bool $newLines = true, bool $associative = null): string {

        $newLine = $newLines ? self::NEW_LINE : '';
        $subLevel = $level + 1;
        $tab = $newLines ? str_repeat(self::TAB, $subLevel) : ' ';
        $string = '[' . ($array ? $newLine . ($newLines ? $tab : '') : '');

        $i = 0;
        $count = count($array);
        $associative = $associative !== null ? $associative : self::isAssociativeArray($array);

        foreach ($array as $key => $value) {

            $i++;
            
            $string .= ($associative ? self::writeValueToPhp($key, 0, $doubleQuotes) . ' => ' : '') . self::writeValueToPhp($value, $subLevel, $doubleQuotes, $newLines) . ($i == $count ? '' : ',' . $newLine . $tab);
        }

        $string .= ($array ? $newLine . ($newLines ? str_repeat(self::TAB, $level) : '') : '')  . ']';
        
        return $string;
    }

    /**
     * Convert something to PHP declaration
     *
     * @param mixed $value
     * @param int $level
     * @param bool $doubleQuotes
     * @param bool $newLines
     * @param bool|null $associative
     * @return string
     */
    public static function writeValueToPhp($value, int $level = 0, bool $doubleQuotes = false, bool $newLines = true, bool $associative = null): string {

        $quote = $doubleQuotes ? '"' : '\'';

        if (is_string($value)) {

            if (substr($value, 0, strlen(self::UNESCAPE)) === self::UNESCAPE) {

                $value = str_replace(self::UNESCAPE, '', $value);
            }
            else {

                $value = $quote . addcslashes($value, $quote) . $quote;
            }
        }
        else if (is_bool($value)) {

            $value = $value ? 'true' : 'false';
        }
        else if (is_null($value)) {

            $value = 'null';
        }
        else if (is_array($value)) {

            $value = self::writeArrayToPhp($value, $level, $doubleQuotes, $newLines, $associative);
        }
        else if (is_object($value)) {

            $value = $quote . (method_exists($value, '__toString') ? (string)$value : '') . $quote;
        }
        
        return (string)$value;
    }

    /**
     * Check if array is associative
     *
     * @param array $array
     * @return bool
     */
    public static function isAssociativeArray(array $array) {

        return [] === $array ? false : array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Get current theme
     *
     * @return string $theme
     */
    public static function getTheme() {

        $theme = 'medkit-theme-malabar';
        $path = base_path('vendor/mediactive-digital');
        $dir = new \DirectoryIterator($path);

        foreach ($dir as $fileinfo) {

            if ($fileinfo->isDir() && strpos($basename = $fileinfo->getBasename(), 'theme') !== false) {

                $theme = $basename;
            }
        }

        return $theme;
    }

    /**
     * Retourne le langage en anglais depuis une locale (code sur 2 lettres ISO 639-1)
     * Utilisé pour la traduction des datatables
     *
     * @param string $locale
     * @return string $language
     */
    public static function getLanguage(string $locale) {

        $language = isset(self::LANGUAGES[$locale]) ? self::LANGUAGES[$locale] : '';

        return $language;
    }

    /**
     * Formatage numérique selon la locale
     *
     * @param mixed $value
     * @param null|int $decimalPlaces
     * @param string $locale
     * @return string $return
     */
    public static function numberFormat($value, int $decimalPlaces = null, string $locale = ''): string {

        $return = '';

        if (is_numeric($value)) {

            $value = (float)$value;
            $separators = self::getNumberSeparators($locale);
            $return = number_format($value, Str::contains($value, '.') ? ($decimalPlaces !== null ? $decimalPlaces : Str::length(Str::after($value, '.'))) : 0, $separators['decimal'], $separators['thousands']); 
        }

        return $return;
    }

    /**
     * Retourne les séparateurs numériques selon la locale
     *
     * @param string $locale
     * @return array $separators
     */
    public static function getNumberSeparators(string $locale = ''): array {

        $locale = $locale ?: LaravelGettext::getLocale();

        $separators = [
            'thousands' => ',',
            'decimal' => '.'
        ];

        if ($locale == 'fr') {

            $separators = [
                'thousands' => ' ',
                'decimal' => ','
            ];
        }

        return $separators;
    }

    /**
     * Nettoye un template Laravel Generator
     *
     * @param string $template
     * @return string $template
     */
    public static function cleanTemplate(string $template): string {

        $template = preg_replace('/\t/', self::TAB, $template);
        $template = preg_replace('/(\S) +(\S)/', "$1 $2", $template);
        $template = preg_replace('/[^\S' . self::NEW_LINE . ']+' . self::NEW_LINE . '/', self::NEW_LINE, $template);
        $template = preg_replace('/(?:' . self::NEW_LINE . '([^\S]*)){2,}/', self::NEW_LINE . self::NEW_LINE . "$1", $template);
        $template = preg_replace('/[\s]*?' . self::NEW_LINE . '[\s]*?' . self::NEW_LINE . '([\s]*?[})\];]+[\s]*)/', self::NEW_LINE . "$1", $template);
        $template = preg_replace('/[\s]*?' . self::NEW_LINE . '[\s]*?' . self::NEW_LINE . '([\s]*?[})\];]+[\s]*)/', self::NEW_LINE . "$1", $template);
        $template = trim($template) . self::NEW_LINE;

        return $template;
    }
}
