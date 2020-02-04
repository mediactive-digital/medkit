<?php

namespace MediactiveDigital\MedKit\Traits;

use Carbon\Carbon;

use MediactiveDigital\MedKit\Helpers\Helper;

use DB;
use Str;

trait Form {

    /** 
     * @var int 
     */
    private static $defaultChoiceLimit = 100;

    /** 
     * Format datetime.
     *
     * @param mixed $date
     * @return \Closure
     */
    private function formatDateTime() {

        return function($date) {

            return $date ? Carbon::parse($date)->format('Y-m-d\TH:i:s') : $date;
        };
    }

    /** 
     * Format JSON.
     *
     * @param string $key
     * @param mixed $array
     * @return \Closure
     */
    private function formatJson(string $key = 'value') {

        return function($array) use ($key) {

            $value = is_array($array) && $array ? (isset($array[$key]) ? $array[$key] : reset($array)) : $array;
            $value = is_string($value) ? $value : '';

            return $value;
        };
    }

    /** 
     * Format null.
     *
     * @param mixed $value
     * @return \Closure
     */
    private function formatNull() {

        return function($value) {

            return null;
        };
    }

    /** 
     * Set attribute.
     *
     * @param mixed $createValue
     * @param mixed $updateValue
     * @return mixed
     */
    private function setAttribute($createValue, $updateValue = null) {

        return $this->model ? ($updateValue !== null ? $updateValue : $createValue) : $createValue;
    }

    /** 
     * Get select choices.
     *
     * @param string $table
     * @param string $label
     * @param int $limit
     * @return array $choices 
     */
    private function getChoices(string $table, string $label = '', int $limit = 0): array {

        $choices = [];

        if (($table = Helper::getTableName($table)) && ($primary = Helper::getTablePrimaryName($table))) {

            $label = Helper::getTableLabelName($table, $label);
            $select = $label;
            $limit = $limit <= 0 ? self::$defaultChoiceLimit : $limit;

            if (!$label) {

                $label = 'label';
                $select = DB::raw('CONCAT(\'' . addcslashes(Str::ucfirst(str_replace('_', ' ', Str::singular(Str::lower($table)))), '\'') . ' ' . '\', `' . $primary . '`)  AS `' . $label . '`');
            }

            $relations = DB::table($table)->select([$primary, $select])->orderBy($label)->limit($limit);

            if ($this->model && ($selfTable = $this->model->getTable()) && $selfTable == $table && ($primaryKey = Helper::getTablePrimaryName($selfTable))) {

                $relations->where($primaryKey, '!=', $this->model->{$primaryKey});
            }

            $relations = $relations->get();
            $choices = $relations ? $relations->pluck($label, $primary)->toArray() : $choices;
        }

        return $choices;
    }

    /**
     * Get Bootstrap 4 badges select choices.
     *
     * @return array
     */
    private function getBadgesChoices() {

        return [ 
            'primary' => 'Primary',  
            'secondary' => 'Secondary',  
            'success' => 'Success',  
            'danger' => 'Danger',  
            'warning' => 'Warning',  
            'info' => 'Info',  
            'light' => 'Light',  
            'dark' => 'Dark'
        ];
    }

    /**
     * Create translatable fields and add them to the form.
     *
     * @param string $name
     * @param string $type
     * @param array $options
     * @param bool $modify
     * @return void
     */
    private function addTranslatable(string $name, string $type = 'text', array $options = [], bool $modify = false) {

        $options['subtype'] = $type;

        $this->add($name, 'translatable', $options, $modify);
    }
}
