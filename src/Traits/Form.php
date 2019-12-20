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

            $relations = DB::table($table)->select([$primary, $select])->orderBy($label)->limit($limit)->get();
            $choices = $relations ? $relations->pluck($label, $primary)->toArray() : $choices;
        }

        return $choices;
    }
}
