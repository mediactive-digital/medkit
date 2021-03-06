<?php

namespace MediactiveDigital\MedKit\Traits;

use Illuminate\Database\Eloquent\Builder;

use MediactiveDigital\MedKit\Helpers\TranslationHelper;
use MediactiveDigital\MedKit\Helpers\FormatHelper;
use MediactiveDigital\MedKit\Helpers\Helper;

use Str;
use LaravelGettext;

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
     * Edit json column.
     *
     * @param string|array|null $value
     * @return string
     */
    private function editJsonColumn($value): string {

        return is_array($value) ? FormatHelper::formatArraytoJson($value) : (is_string($value) ? $value : '');
    }

    /**
     * Filter boolean column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @return void
     */
    private function filterBooleanColumn(Builder $query, string $keyword, $column, bool $raw = false) {

    	$column = $this->wrapColumn($query, $column, $raw);
        $true = addcslashes(_i('Vrai'), '\'');
        $false = addcslashes(_i('Faux'), '\'');

    	$query->whereRaw('LOWER(IF(' . $column . ' = 1, \'' . $true . '\', IF(' . $column . ' = 0, \'' . $false . '\', NULL))) LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter datetime column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @return void
     */
    private function filterDateTimeColumn(Builder $query, string $keyword, $column, bool $raw = false) {

        $this->filterDateTime($query, $keyword, $column, _i('%d/%m/%Y %H:%i:%s'), $raw);
    }

    /**
     * Filter date column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @return void
     */
    private function filterDateColumn(Builder $query, string $keyword, $column, bool $raw = false) {

        $this->filterDateTime($query, $keyword, $column, _i('%d/%m/%Y'), $raw);
    }

    /**
     * Filter time column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @return void
     */
    private function filterTimeColumn(Builder $query, string $keyword, $column, bool $raw = false) {

        $this->filterDateTime($query, $keyword, $column, _i('%H:%i:%s'), $raw);
    }

    /**
     * Filter datetime / date / time column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $format
     * @param bool $raw
     * @return void
     */
    private function filterDateTime(Builder $query, string $keyword, $column, string $format, bool $raw = false) {

        $column = $this->wrapColumn($query, $column, $raw);
        $format = addcslashes($format, '\'');

        $query->whereRaw('LOWER(DATE_FORMAT(' . $column . ', \'' . $format . '\')) LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter float column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @return void
     */
    private function filterFloatColumn(Builder $query, string $keyword, $column, bool $raw = false) {

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
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @return void
     */
    private function filterIntegerColumn(Builder $query, string $keyword, $column, bool $raw = false) {

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
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @param bool $translatable
     * @return void
     */
    private function filterFkIntegerColumn(Builder $query, string $keyword, $column, bool $raw = false, bool $translatable = false) {

        if (!$raw && is_string($column)) {

            $columnValues = explode('.', $column);
            $table = $tableAlias = Helper::getTableName($columnValues[0]);
            $column = '';
            $raw = true;

            if (!$table && Str::endsWith($columnValues[0], '_join')) {

                $table = Helper::getTableName(Str::replaceLast('_join', '', $columnValues[0]));
                $tableAlias = $columnValues[0];
            }

            if ($table) {

                $label = Helper::getTableLabelName($table, isset($columnValues[1]) ? $columnValues[1] : '');

                if ($label) {

                    if ($translatable) {

                        $column = TranslationHelper::getTranslatableQuery($label, $tableAlias);
                    }
                    else {

                        $column = $tableAlias . '.' . $label;
                        $raw = false;
                    }
                }
                elseif (($primary = Helper::getTablePrimaryName($table))) {

                    $column = 'CONCAT(\'' . addcslashes(Str::ucfirst(str_replace('_', ' ', Str::singular(Str::lower($table)))), '\'') . ' ' . '\', `' . $tableAlias . '`.`' . $primary . '`)';
                }
            }
        }

        if ($column) {

            $column = $this->wrapColumn($query, $column, $raw);
            $rawQuery = 'LOWER(' . $column . ') LIKE ?';
            $parameters = ['%' . $keyword . '%'];

            $query->whereRaw($rawQuery, $parameters);
        }
    }

    /**
     * Filter translatable foreign key integer column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param bool $raw
     * @return void
     */
    private function filterTranslatableFkIntegerColumn(Builder $query, string $keyword, $column, bool $raw = false) {

        $this->filterFkIntegerColumn($query, $keyword, $column, $raw, true);
    }

    /**
     * Filter enum column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param array $values
     * @param bool $raw
     * @return void
     */
    private function filterEnumColumn(Builder $query, string $keyword, $column, array $values = [], bool $raw = false) {

        $this->filterEnumOrChoiceColumn($query, $keyword, $column, $values, false, $raw);
    }

    /**
     * Filter choice column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param array $values
     * @param bool $raw
     * @return void
     */
    private function filterChoiceColumn(Builder $query, string $keyword, $column, array $values = [], bool $raw = false) {

        $this->filterEnumOrChoiceColumn($query, $keyword, $column, $values, true, $raw);
    }

    /**
     * Filter enum or choice column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param array $values
     * @param bool $associative
     * @param bool $raw
     * @return void
     */
    private function filterEnumOrChoiceColumn(Builder $query, string $keyword, $column, array $values = [], bool $associative = false, bool $raw = false) {

        if ($values) {

            $column = $this->wrapColumn($query, $column, $raw);

            if ($associative) {

                $when = '';

                foreach ($values as $value => $label) {

                    $when .= ($when ? ' ' : '') . 'WHEN ' . $column . ' = \'' . addcslashes($value, '\'') . '\' THEN \'' . addcslashes($label, '\'') . '\'';
                }

                $rawQuery = 'CASE ' . $when . ' END';
            }
            else {

                $list = '';

                foreach ($values as $label) {

                    $list .= ($list ? ', ' : '') . '\'' . addcslashes($label, '\'') . '\'';
                }

                $rawQuery = 'IF(' . $column . ' IN(' . $list . '), ' . $column . ', NULL)';
            }

            $rawQuery = 'LOWER(' . $rawQuery . ') LIKE ?';
            $parameters = ['%' . $keyword . '%'];

            $query->whereRaw($rawQuery, $parameters);
        }
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
