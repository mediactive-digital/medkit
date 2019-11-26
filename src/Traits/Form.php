<?php

namespace MediactiveDigital\MedKit\Traits;

use Carbon\Carbon;

trait Form {

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
}
