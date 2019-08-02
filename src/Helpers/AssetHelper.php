<?php

namespace MediactiveDigital\MedKit\Helpers;

use \Illuminate\Support\Facades\Config;
use \Illuminate\Support\Facades\File;

class AssetHelper
{
    const CSS   = 'css';
    const JS    = 'js';

    /**
     * Add CSS files
     * @param mixed $files Array or string
     * @return string
     */
    public static function addCss($files): string
    {
        return self::generateTag($files, self::CSS);
    }

    /**
     * Add JS files
     * @param mixed $files Array or string
     * @return string
     */
    public static function addJs($files): string
    {
        return self::generateTag($files, self::JS);
    }


    /**
     * Generate CSS / JS tags
     * @param mixed $files Array or string
     * @param string $type
     * @return string
     */
    protected static function generateTag($files, $type): string
    {
        $result = '';
        // On crée un array de files
        $files = isset($files['file']) ? [$files] : (array)$files;

        // Tags JS ou CSS ?
        $startTag = ($type == self::CSS) ? '<link href="' : '<script src="';
        $endTag = ($type == self::CSS) ? '>' : '></script>';
        $defaultAttributes = ($type == self::CSS) ? ['rel' => 'stylesheet', 'type' => 'text/css'] : ['type' => 'text/javascript'];

        // On recupere la config depuis le fichier json
        $configs = app('assetConfJson');

        foreach ($files as $fileValue) {

            $attributes = '"';
            $fileValue = (array)$fileValue;
            $file = isset($fileValue[0]) ? $fileValue[0] : (isset($fileValue['file']) ? $fileValue['file'] : '');
            $attributesArray = isset($fileValue['attributes']) ? (array)$fileValue['attributes'] : [];

            if (!filter_var($file, FILTER_VALIDATE_URL)) {

                $values = $configs[$file];

                $fileAlias = isset($values[0]) ? $values[0] : (isset($values['file']) ? $values['file'] : '');
                $isUrl = $fileAlias ? filter_var($fileAlias, FILTER_VALIDATE_URL) : false;
                $file = $fileAlias ? $fileAlias : $file;

                if (!$isUrl) {

                    $filePath = public_path().'/'.$file;
                    $timeStamp = File::exists($filePath) ? File::lastModified($filePath) : false;

                    // Add timestamp to filename to avoid cache issues
                    // You will need to edit your .htaccess, see README.md
                    $fileInfo = pathinfo( $fileAlias );
                    if( in_array( $fileInfo['extension'], ['js','css'] ) ){
                        $fileAlias = $fileInfo['dirname']."/".$fileInfo['filename'].".".$timeStamp.".".$fileInfo['extension'];
                    }
                    $file = asset($fileAlias);
                }

                $attributesArray += isset($values['attributes']) ? (array)$values['attributes'] : [];
            }

            $attributesArray += $defaultAttributes;

            foreach ($attributesArray as $attribute => $value) {

                $attributes .= ' '.$attribute.($value !== null ? '="'.$value.'"' : '');
            }

            $result .= $startTag.$file.$attributes.$endTag."\r\n";
        }

        return $result ? rtrim($result, "\r\n") : $result;
    }

}
