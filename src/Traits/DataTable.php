<?php

namespace MediactiveDigital\MedKit\Traits;

use Illuminate\Database\Eloquent\Builder;

use MediactiveDigital\MedKit\Helpers\FormatHelper;
use MediactiveDigital\MedKit\Helpers\Helper;

trait DataTable {

    /**
     * Edit boolean column.
     *
     * @param int|null $value
     * @return string
     */
    private function editBooleanColumn($value): string {

        return $value === true ? _i('Vrai') : ($value === false ? _i('Faux') : '');
    }

    /**
     * Edit datetime column.
     *
     * @param string|null $value
     * @return string
     */
    private function editDateTimeColumn($value): string {

        return $value ? date(_i('d/m/Y H:i:s'), strtotime($value)) : '';
    }

    /**
     * Edit date column.
     *
     * @param string|null $value
     * @return string
     */
    private function editDateColumn($value): string {

        return $value ? date(_i('d/m/Y'), strtotime($value)) : '';
    }

    /**
     * Edit time column.
     *
     * @param string|null $value
     * @return string
     */
    private function editTimeColumn($value): string {

        return $value ? date(_i('H:i:s'), strtotime($value)) : '';
    }

    /**
     * Edit float column.
     *
     * @param int|float|null $value
     * @return string
     */
    private function editFloatColumn($value): string {

        return $this->editNumericColumn($value);
    }

    /**
     * Edit integer column.
     *
     * @param int|null $value
     * @return string
     */
    private function editIntegerColumn($value): string {

        return $this->editNumericColumn($value);
    }

    /**
     * Edit numeric column.
     *
     * @param int|float|null $value
     * @return string
     */
    private function editNumericColumn($value): string {

        return FormatHelper::numberFormat($value);
    }

    /**
     * Filter boolean column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterBooleanColumn(Builder $query, $column, string $keyword, bool $raw = false) {

    	$column = $this->wrapColumn($query, $column, $raw);
        $true = addcslashes(_i('Vrai'), '\'');
        $false = addcslashes(_i('Faux'), '\'');

    	$query->whereRaw('LOWER(IF(' . $column . ' = 1, \'' . $true . '\', IF(' . $column . ' = 0, \'' . $false . '\', NULL))) LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter datetime column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterDateTimeColumn(Builder $query, $column, string $keyword, bool $raw = false) {

        $this->filterDateTime($query, $column, $keyword, _i('%d/%m/%Y %H:%i:%s'), $raw);
    }

    /**
     * Filter date column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterDateColumn(Builder $query, $column, string $keyword, bool $raw = false) {

        $this->filterDateTime($query, $column, $keyword, _i('%d/%m/%Y'), $raw);
    }

    /**
     * Filter time column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterTimeColumn(Builder $query, $column, string $keyword, bool $raw = false) {

        $this->filterDateTime($query, $column, $keyword, _i('%H:%i:%s'), $raw);
    }

    /**
     * Filter datetime / date / time column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param string $format
     * @param bool $raw
     * @return void
     */
    private function filterDateTime(Builder $query, $column, string $keyword, string $format, bool $raw = false) {

        $column = $this->wrapColumn($query, $column, $raw);
        $format = addcslashes($format, '\'');

        $query->whereRaw('LOWER(DATE_FORMAT(' . $column . ', \'' . $format . '\')) LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter float column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterFloatColumn(Builder $query, $column, string $keyword, bool $raw = false) {

        $column = $this->wrapColumn($query, $column, $raw);
        $separators = FormatHelper::getNumberSeparators();
        $thousandsSeparator = addcslashes($separators['thousands'], '\'');
        $decimalSeparator = addcslashes($separators['decimal'], '\'');

        $rawQuery = $column . ' LIKE ?';
        $parameters = ['%' . $keyword . '%'];
        $formatNumber = 'FORMAT(' . $column . ', LENGTH(RIGHT(' . $column . ', INSTR(REVERSE(' . $column . '), \'.\') -1)))';

        if ($thousandsSeparator != '.') {

        	$replaceStart = $replaceEnd = '';

	    	if ($thousandsSeparator != ',') {

	    		$replaceStart = 'LOWER(REPLACE(';
	    		$replaceEnd = ', \',\', \'' . $thousandsSeparator . '\'))';
	    	}

        	$rawQuery .= ' OR ' . $replaceStart . $formatNumber . $replaceEnd . ' LIKE ?';
        	$parameters[] = $parameters[0];
        }

        if ($decimalSeparator != '.') {

        	$rawQuery .= ' OR LOWER(REPLACE(' . $column . ', \'.\', \'' . $decimalSeparator . '\')) LIKE ?';
        	$parameters[] = $parameters[0];
        }

        if ($thousandsSeparator != ',' && $decimalSeparator != '.') {

        	$rawQuery .= ' OR LOWER(REPLACE(REPLACE(REPLACE(' . $formatNumber . ', \',\', \'##THOUSANDS##\'), \'.\', \'' . $decimalSeparator . '\'), \'##THOUSANDS##\', \'' . $thousandsSeparator . '\')) LIKE ?';
            $parameters[] = $parameters[0];
        }

        $query->whereRaw($rawQuery, $parameters);
    }

    /**
     * Filter integer column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterIntegerColumn(Builder $query, $column, string $keyword, bool $raw = false) {

        $column = $this->wrapColumn($query, $column, $raw);
        $separators = FormatHelper::getNumberSeparators();
        $thousandsSeparator = addcslashes($separators['thousands'], '\'');

        $rawQuery = $column . ' LIKE ?';
        $parameters = ['%' . $keyword . '%'];

        if ($thousandsSeparator != ',') {

        	$rawQuery .= ' OR LOWER(REPLACE(FORMAT(' . $column . ', 0), \',\', \'' . $thousandsSeparator . '\')) LIKE ?';
        	$parameters[] = $parameters[0];
        }

        $query->whereRaw($rawQuery, $parameters);
    }

    /**
     * Filter foreign key integer column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string $table
     * @param string $keyword
     * @param string $label
     * @param bool $raw
     * @return void
     */
    private function filterFkIntegerColumn(Builder $query, $table, string $keyword, string $label = '', bool $raw = false) {

        $column = $table;

        if (is_string($table)) {

            $table = Helper::getTableName($table);

            if ($table) {

                $label = Helper::getTableLabelName($table, $label);

                if ($label) {

                    $column = $table . '.' . $label;
                    $raw = false;
                }
                else {

                    if (($primary = Helper::getTablePrimaryName($table))) {

                        $column = 'CONCAT(\'' . addcslashes(Str::ucfirst(str_replace('_', ' ', Str::singular(Str::lower($table)))), '\'') . ' ' . '\', `' . $table . '`.`' . $primary . '`)';
                    }
                    else {

                        $column = '\'\'';
                    }

                    $raw = true;
                }
            }
            else {

                $column = '\'\'';
                $raw = true;
            }
        }

        $column = $this->wrapColumn($query, $column, $raw);
        $rawQuery = $column . ' LIKE ?';
        $parameters = ['%' . $keyword . '%'];

        $query->whereRaw($rawQuery, $parameters);
    }

    /**
     * Wrap column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Query\Expression|string
     * @param bool $raw
     * @return string
     */
    private function wrapColumn(Builder $query, $column, bool $raw = false): string {

		return $raw ? (string)$column : $query->getConnection()->getQueryGrammar()->wrap($column);
    }
}
