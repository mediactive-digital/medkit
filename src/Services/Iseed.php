<?php

namespace MediactiveDigital\MedKit\Services;

use Orangehill\Iseed\Iseed as OrangehillIseed;

class Iseed extends OrangehillIseed
{

    /**
     * Desired indent for the code.
     * For tabulator use \t
     * Double quotes are mandatory!
     *
     * @var string
     */
    private $indentCharacter = "    ";

    /**
     * Prettify a var_export of an array
     * 
     * @param array $array
     * 
     * @return string
     */
    protected function prettifyArray($array, $indexed = true)
    {

        $content = ($indexed)
            ? var_export($array, true)
            : preg_replace("/[0-9]+ \=\>/i", '', var_export($array, true));

        $lines = explode("\n", $content);

        $inString = false;
        $tabCount = 3;
        for ($i = 1; $i < count($lines); $i++) {
            if (!$inString) {
                $lines[$i] = ltrim($lines[$i]);
            }

            //Check for closing bracket
            if (strpos($lines[$i], ')') !== false) {
                $tabCount--;
            }

            //Insert tab count
            if ($inString === false) {
                for ($j = 0; $j < $tabCount; $j++) {
                    $lines[$i] = substr_replace($lines[$i], $this->indentCharacter, 0, 0);
                }
            }

            for ($j = 0; $j < strlen($lines[$i]); $j++) {
                //skip character right after an escape \
                if ($lines[$i][$j] == '\\') {
                    $j++;
                }
                //check string open/end
                else if ($lines[$i][$j] == '\'') {
                    $inString = !$inString;
                }
            }

            //check for openning bracket
            if (strpos($lines[$i], '(') !== false) {
                $tabCount++;
            }
        }

        $content = implode("\n", $lines);

        return $content;
    }
}
