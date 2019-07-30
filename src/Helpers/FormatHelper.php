<?php

namespace MediactiveDigital\MedKit\Helpers;
use \Carbon\Carbon;

class FormatHelper {

    const START_YEAR = 2017;

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
     * @param array $arr
     * @param integer $level 
     * @return string
     */
    public static function writeArrayToPhp( array $arr, $level=1 ){
        $tab = "    ";
        $tab = str_repeat( $tab, $level );
        $str = "";
        $linebreak = ( $level == 2 ) ? "\n" : "";
        foreach( $arr as $key => $value ){
            
            $str .= $linebreak.$tab."'".$key."' => ".self::writeValueToPhp( $value, $level ).",\n";
        }
        
        return $str;
    }


    /**
     * Convert something to PHP declaration
     *
     * @param various $value
     * @param integer $level
     * @return string
     */
    public static function writeValueToPhp( $value, $level ): string
    {
        $tab = "    ";
        $tab = str_repeat( $tab, $level );

        if (is_string($value) && strpos($value, "'") === false) {
            $replaceValue = "\"".$value."\"";
        }
        elseif (is_string($value) && strpos($value, '"') === false) {
            $replaceValue = '"'.$value.'"';
        }
        elseif (is_bool($value)) {
            $replaceValue = ($value ? 'true' : 'false');
        }
        elseif (is_null($value)) {
            $replaceValue = 'null';
        }
        elseif (is_array($value) ) {
            $replaceValue = "[\n".self::writeArrayToPhp( (array)$value, ++$level )."$tab]";
        }
        else {
            $replaceValue = $value;
        }
        $replaceValue = str_replace('$', '\$', $replaceValue);
        
        return $replaceValue;
    }
}