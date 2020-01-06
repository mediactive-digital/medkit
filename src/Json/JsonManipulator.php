<?php

namespace MediactiveDigital\MedKit\Json;

use Composer\Json\JsonManipulator as ComposerJsonManipulator;
use Composer\Json\JsonFile;

use MediactiveDigital\MedKit\Traits\Reflection;

class JsonManipulator extends ComposerJsonManipulator {

    private $newline;
    private $indent;

    use Reflection;

    public function __construct($contents) {

        parent::__construct($contents);
        
        $this->newline = $this->getReflectionProperty('newline');
        $this->indent = $this->getReflectionProperty('indent');
    }

    public function format($data, $depth = 0) {

        if (is_array($data)) {

            reset($data);

            $isNum = is_numeric(key($data));
            $out = ($isNum ? '[' : '{') . $this->newline;
            $elems = array();

            foreach ($data as $key => $val) {

                $elems[] = str_repeat($this->indent, $depth + 2) . ($isNum ? '' : JsonFile::encode($key) . ': ') . $this->format($val, $depth + 1);
            }

            return $out . implode(',' . $this->newline, $elems) . $this->newline . str_repeat($this->indent, $depth + 1) . ($isNum ? ']' : '}');
        }

        return JsonFile::encode($data);
    }
}
