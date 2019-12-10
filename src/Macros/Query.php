<?php

namespace MediactiveDigital\MedKit\Macros;

use Illuminate\Database\Query\Builder as DBqueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;

class Query {

	/**
	 * Register macros to format queries to raw SQL.
	 *
	 * @return void
	 */
	public static function toRawSql() {

		DBqueryBuilder::macro('toRawSql', function() {

		    return array_reduce($this->getBindings(), function($sql, $binding) {

		        return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);

		    }, $this->toSql());
		});

		EloquentQueryBuilder::macro('toRawSql', function() {

		    return ($this->getQuery()->toRawSql());
		});
	}
}
