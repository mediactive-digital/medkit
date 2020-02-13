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
        'cu' => 'Old Church Slavonic',
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
        'kl' => 'Greenlandic',
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
        'lu' => 'Luba',
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
        'rc' => 'Reunionese',
        'rm' => 'Romansh',
        'rn' => 'Kirundi',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'rw' => 'Kinyarwanda',
        'sa' => 'Sanskrit',
        'sc' => 'Sardinian',
        'sd' => 'Sindhi',
        'se' => 'Northern Sami',
        'sg' => 'Sango',
        'sh' => 'Serbo-Croatian',
        'si' => 'Sinhalese',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'sm' => 'Samoan',
        'sn' => 'Shona',
        'so' => 'Somali',
        'sq' => 'Albanian',
        'sr' => 'Serbian',
        'ss' => 'Swati',
        'st' => 'Sotho',
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
     * Retourne la liste des traductions des locales (code sur 2 lettres ISO 639-1)
     *
     * @return array $translatedLocales
     */
    public static function getTranslatedLocales() {

        $translatedLocales = [
            'aa' => _i('Afar'),
            'ab' => _i('Abkhaze'),
            'ae' => _i('Avestique'),
            'af' => _i('Afrikaans'),
            'ak' => _i('Akan'),
            'am' => _i('Amharique'),
            'an' => _i('Aragonais'),
            'ar' => _i('Arabe'),
            'as' => _i('Assamais'),
            'av' => _i('Avar'),
            'ay' => _i('Aymara'),
            'az' => _i('Azéri'),
            'ba' => _i('Bachkir'),
            'be' => _i('Biélorusse'),
            'bg' => _i('Bulgare'),
            'bh' => _i('Bihari'),
            'bi' => _i('Bichelamar'),
            'bm' => _i('Bambara'),
            'bn' => _i('Bengali'),
            'bo' => _i('Tibétain'),
            'br' => _i('Breton'),
            'bs' => _i('Bosnien'),
            'ca' => _i('Catalan'),
            'ce' => _i('Tchétchène'),
            'ch' => _i('Chamorro'),
            'co' => _i('Corse'),
            'cr' => _i('Cri'),
            'cs' => _i('Tchèque'),
            'cu' => _i('Vieux-slave'),
            'cv' => _i('Tchouvache'),
            'cy' => _i('Gallois'),
            'da' => _i('Danois'),
            'de' => _i('Allemand'),
            'dv' => _i('Maldivien'),
            'dz' => _i('Dzongkha'),
            'ee' => _i('Ewe'),
            'el' => _i('Grec moderne'),
            'en' => _i('Anglais'),
            'eo' => _i('Espéranto'),
            'es' => _i('Espagnol'),
            'et' => _i('Estonien'),
            'eu' => _i('Basque'),
            'fa' => _i('Persan'),
            'ff' => _i('Peul'),
            'fi' => _i('Finnois'),
            'fj' => _i('Fidjien'),
            'fo' => _i('Féroïen'),
            'fr' => _i('Français'),
            'fy' => _i('Frison occidental'),
            'ga' => _i('Irlandais'),
            'gd' => _i('Écossais'),
            'gl' => _i('Galicien'),
            'gn' => _i('Guarani'),
            'gu' => _i('Gujarati'),
            'gv' => _i('Mannois'),
            'ha' => _i('Haoussa'),
            'he' => _i('Haoussa'),
            'hi' => _i('Hindi'),
            'ho' => _i('Hiri Motu'),
            'hr' => _i('Croate'),
            'ht' => _i('Créole haïtien'),
            'hu' => _i('Hongrois'),
            'hy' => _i('Arménien'),
            'hz' => _i('Héréro'),
            'ia' => _i('Interlingua'),
            'id' => _i('Indonésien'),
            'ie' => _i('Occidental'),
            'ig' => _i('Igbo'),
            'ii' => _i('Yi'),
            'ik' => _i('Inupiak'),
            'io' => _i('Ido'),
            'is' => _i('Islandais'),
            'it' => _i('Italien'),
            'iu' => _i('Inuktitut'),
            'ja' => _i('Japonais'),
            'jv' => _i('Javanais'),
            'ka' => _i('Géorgien'),
            'kg' => _i('Kikongo'),
            'ki' => _i('Kikuyu'),
            'kj' => _i('Kuanyama'),
            'kk' => _i('Kazakh'),
            'kl' => _i('Groenlandais'),
            'km' => _i('Khmer'),
            'kn' => _i('Kannada'),
            'ko' => _i('Coréen'),
            'kr' => _i('Kanouri'),
            'ks' => _i('Cachemiri'),
            'ku' => _i('Kurde'),
            'kv' => _i('Komi'),
            'kw' => _i('Cornique'),
            'ky' => _i('Kirghiz'),
            'la' => _i('Latin'),
            'lb' => _i('Luxembourgeois'),
            'lg' => _i('Ganda'),
            'li' => _i('Limbourgeois'),
            'ln' => _i('Lingala'),
            'lo' => _i('Lao'),
            'lt' => _i('Lituanien'),
            'lu' => _i('Luba'),
            'lv' => _i('Letton'),
            'mg' => _i('Malgache'),
            'mh' => _i('Marshallais'),
            'mi' => _i('Maori de Nouvelle-Zélande'),
            'mk' => _i('Macédonien'),
            'ml' => _i('Malayalam'),
            'mn' => _i('Mongol'),
            'mr' => _i('Marathi'),
            'ms' => _i('Malais'),
            'mt' => _i('Maltais'),
            'my' => _i('Birman'),
            'na' => _i('Nauruan'),
            'nb' => _i('Norvégien Bokmål'),
            'nd' => _i('Sindebele'),
            'ne' => _i('Népalais'),
            'ng' => _i('Ndonga'),
            'nl' => _i('Néerlandais'),
            'nn' => _i('Norvégien Nynorsk'),
            'no' => _i('Norvégien'),
            'nr' => _i('Nrebele'),
            'nv' => _i('Navajo'),
            'ny' => _i('Chichewa'),
            'oc' => _i('Occitan'),
            'oj' => _i('Ojibwé'),
            'om' => _i('Oromo'),
            'or' => _i('Oriya'),
            'os' => _i('Ossète'),
            'pa' => _i('Pendjabi'),
            'pi' => _i('Pali'),
            'pl' => _i('Polonais'),
            'ps' => _i('Pachto'),
            'pt' => _i('Portugais'),
            'qu' => _i('Quechua'),
            'rc' => _i('Créole Réunionnais'),
            'rm' => _i('Romanche'),
            'rn' => _i('Kirundi'),
            'ro' => _i('Roumain'),
            'ru' => _i('Russe'),
            'rw' => _i('Kinyarwanda'),
            'sa' => _i('Sanskrit'),
            'sc' => _i('Sarde'),
            'sd' => _i('Sindhi'),
            'se' => _i('Same du Nord'),
            'sg' => _i('Sango'),
            'sh' => _i('Serbo-croate'),
            'si' => _i('Cingalais'),
            'sk' => _i('Slovaque'),
            'sl' => _i('Slovène'),
            'sm' => _i('Samoan'),
            'sn' => _i('Shona'),
            'so' => _i('Somali'),
            'sq' => _i('Albanais'),
            'sr' => _i('Serbe'),
            'ss' => _i('Swati'),
            'st' => _i('Sotho du Sud'),
            'su' => _i('Soundanais'),
            'sv' => _i('Suédois'),
            'sw' => _i('Swahili'),
            'ta' => _i('Tamoul'),
            'te' => _i('Télougou'),
            'tg' => _i('Tadjik'),
            'th' => _i('Thaï'),
            'ti' => _i('Tigrigna'),
            'tk' => _i('Turkmène'),
            'tl' => _i('Tagalog'),
            'tn' => _i('Tswana'),
            'to' => _i('Tongien'),
            'tr' => _i('Turc'),
            'ts' => _i('Tsonga'),
            'tt' => _i('Tatar'),
            'tw' => _i('Twi'),
            'ty' => _i('Tahitien'),
            'ug' => _i('Ouïghour'),
            'uk' => _i('Ukrainien'),
            'ur' => _i('Ourdou'),
            'uz' => _i('Ouzbek'),
            've' => _i('Venda'),
            'vi' => _i('Vietnamien'),
            'vo' => _i('Volapük'),
            'wa' => _i('Wallon'),
            'wo' => _i('Wolof'),
            'xh' => _i('Xhosa'),
            'yi' => _i('Yiddish'),
            'yo' => _i('Yoruba'),
            'za' => _i('Zhuang'),
            'zh' => _i('Chinois'),
            'zu' => _i('Zoulou')
        ];

        return $translatedLocales;
    }

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
     * Retourne la traduction d'une locale (code sur 2 lettres ISO 639-1)
     *
     * @param string $locale
     * @return string $translatedLocale
     */
    public static function getLocaleTranslation(string $locale = '') {

        $locale = $locale ?: LaravelGettext::getLocale();
        $locales = self::getTranslatedLocales();
        $translatedLocale = isset($locales[$locale]) ? $locales[$locale] : '';

        return $translatedLocale;
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
                'thousands' => _i(' '),
                'decimal' => ','
            ];
        }

        return $separators;
    }

    /**
     * Formate un tableau en JSON
     *
     * @param array $array
     * @param bool $prettyPrint
     * @param bool $unescapedUnicode
     * @param bool $unescapedSlashes
     * @param bool $forceObject
     * @return string
     */
    public static function formatArraytoJson(array $array, $prettyPrint = true, $unescapedUnicode = true, $unescapedSlashes = true, $forceObject = true): string {

        return json_encode($array, ($prettyPrint ? JSON_PRETTY_PRINT : null) | 
            ($unescapedUnicode ? JSON_UNESCAPED_UNICODE : null) | 
            ($unescapedSlashes ? JSON_UNESCAPED_SLASHES : null) | 
            ($forceObject && !$array ? JSON_FORCE_OBJECT : null));
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
        $template = preg_replace('/(\(|\[)\s*?' . self::NEW_LINE . '\s*?' . self::NEW_LINE . '(\s*)/', "$1" . self::NEW_LINE . "$2", $template);
        $template = preg_replace('/\s*?' . self::NEW_LINE . '\s*?' . self::NEW_LINE . '(\s*?[})\];]+\s*)/', self::NEW_LINE . "$1", $template);
        $template = preg_replace('/\s*?' . self::NEW_LINE . '\s*?' . self::NEW_LINE . '(\s*?[})\];]+\s*)/', self::NEW_LINE . "$1", $template);
        $template = trim($template) . self::NEW_LINE;

        return $template;
    }

    /**
     * Génère des attributs HTML sous forme de chaîne de caractères depuis un tableau
     *
     * @param array $htmlAttributes
     * @return string $renderedHtmlAttributes
     */
    public static function renderHtmlAttributes(array $htmlAttributes): string {

        $renderedHtmlAttributes = '';

        array_walk($htmlAttributes, function($value, $key) use (&$renderedHtmlAttributes) { 

            $renderedHtmlAttributes = $renderedHtmlAttributes ? rtrim($renderedHtmlAttributes) : $renderedHtmlAttributes;
            $renderedHtmlAttributes .= ($renderedHtmlAttributes ? ' ' : '') . ($key && is_string($key) ? $key . '="' . $value . '"' : $value); 
        });

        return $renderedHtmlAttributes;
    }

    /**
     * Génère un tableau imbriqué depuis un tableau "à plat".
     *
     * @param array $flatArray
     * @param int $parentId
     * @param bool $unsetParentKey
     * @param string $parentKey
     * @param string $childrenKey
     * @param string $idKey
     * @return array $branch
     */
    public static function buildTreeFromFlatArray(array $flatArray, int $parentId = null, bool $unsetParentKey = true, string $parentKey = 'parent_id', string $childrenKey = 'children', $idKey = 'id'): array {
    
        $branch = [];

        foreach ($flatArray as $element) {

            if ($element[$parentKey] == $parentId) {

                $children = self::buildTreeFromFlatArray($flatArray, $element[$idKey], $unsetParentKey, $parentKey, $childrenKey, $idKey);
                
                if ($children) {

                    $element[$childrenKey] = $children;
                }

                if ($unsetParentKey) {

                    unset($element[$parentKey]);
                }
                
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
