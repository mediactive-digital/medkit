<?php

namespace MediactiveDigital\MedKit\Traits;

use MediactiveDigital\MedKit\Helpers\FormatHelper;

trait DataTable {

	/** 
     * @var \Illuminate\Database\Query\Builder $query
     */
    private $query;

    /**
     * Edit boolean column.
     *
     * @param int|null $value
     * @return string
     */
    private function editBooleanColumn($value): string {

        return $value === 1 ? _i('Vrai') : ($value === 0 ? _i('Faux') : '');
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
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterBooleanColumn($column, string $keyword, bool $raw = false) {

    	$column = $this->wrapColumn($column, $raw);

    	$this->query->whereRaw('IF(' . $column . ' = 1, \'Vrai\', IF(' . $column . ' = 0, \'Faux\', NULL)) LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter datetime column.
     *
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterDateTimeColumn($column, string $keyword, bool $raw = false) {

    	$this->query->whereRaw('DATE_FORMAT(' . $this->wrapColumn($column, $raw) . ', "' . _i('%d/%m/%Y %H:%i:%s') . '") LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter date column.
     *
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterDateColumn($column, string $keyword, bool $raw = false) {

    	$this->query->whereRaw('DATE_FORMAT(' . $this->wrapColumn($column, $raw) . ', "' . _i('%d/%m/%Y') . '") LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter time column.
     *
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterTimeColumn($column, string $keyword, bool $raw = false) {

    	$this->query->whereRaw('DATE_FORMAT(' . $this->wrapColumn($column, $raw) . ', "' . _i('%H:%i:%s') . '") LIKE ?', ['%' . $keyword . '%']);
    }

    /**
     * Filter numeric column.
     *
     * @param \Illuminate\Database\Query\Expression|string $column
     * @param string $keyword
     * @param bool $raw
     * @return void
     */
    private function filterNumericColumn($column, string $keyword, bool $raw = false) {

		$this->query->whereRaw($this->wrapColumn($column, $raw) . ' LIKE ?', ['%' . $this->formatNumericKeyword($keyword) . '%']);
    }

    /**
     * Format keyword for numeric column filtering.
     *
     * @param string $keyword
     * @return string $keyword
     */
    private function formatNumericKeyword(string $keyword): string {

    	$cleanKeyword = preg_replace('/\s+/', '', $keyword);

		preg_match_all('/[^0-9]/', $cleanKeyword, $matches);

		$matches = array_values(array_unique($matches[0]));

		if ($matches && count($matches) <= 2) {
		    
		    $firstCharacter = $matches[0];
		    $secondCharacter = isset($matches[1]) ? $matches[1] : '';
		    $thousandSeparator = $decimalPoint = '';
		    $valid = true;
		    
		    if ($secondCharacter) {
		        
		        $decimalPoint = strrpos($cleanKeyword, $firstCharacter) > strrpos($cleanKeyword, $secondCharacter) ? $firstCharacter : $secondCharacter;
		        
		        if (($valid = mb_substr_count($cleanKeyword, $decimalPoint) == 1)) {
		            
		            $thousandSeparator = $decimalPoint == $firstCharacter ? $secondCharacter : $firstCharacter;
		        }
		    }
		    else if (mb_substr_count($cleanKeyword, $firstCharacter) > 1) {

		    	$thousandSeparator = $firstCharacter;
		    }

		    if ($valid) {

		    	$keyword = $thousandSeparator ? str_replace($thousandSeparator, '', $cleanKeyword) : $cleanKeyword;
		    	$keyword = $decimalPoint ? str_replace($decimalPoint, '.', $keyword) : $keyword;
		    }
		}

		return $keyword;
    }

    /**
     * Wrap column.
     *
     * @param \Illuminate\Database\Query\Expression|string
     * @param bool $raw
     * @return string
     */
    private function wrapColumn($column, bool $raw = false): string {

		return $raw ? (string)$column : $this->query->getGrammar()->wrap($column);
    }
}
