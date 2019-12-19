<?php

namespace MediactiveDigital\MedKit\Traits;

use Carbon\Carbon;

use DB;
use Str;
use Schema;

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

    /** 
     * Get select choices.
     *
     * @param string $relationModel
     * @param string $labelColumn
     * @param int $limit
     * @return array $choices 
     */
    private function getChoices(string $relationModel, string $labelColumn = '', int $limit = 100): array {

        $choices = [];
        $table = Schema::hasTable($relationModel) ? $relationModel : (($table = Str::snake(Str::plural($relationModel))) && Schema::hasTable($table) ? $table : '');

        if (!$table) {

            $classPrefix = '\\' . config('laravel_generator.namespace.model', 'App\Models') . '\\';
            $class = ($class = $classPrefix . $relationModel) && class_exists($class) ? $class : (($class = $classPrefix . Str::studly(Str::singular($relationModel))) && class_exists($class) ? $class : '');

            if ($class) {

                $table = (new $class)->getTable();
                $table = Schema::hasTable($table) ? $table : (($table = Str::snake(Str::plural($table))) && Schema::hasTable($table) ? $table : '');
            }
        }

        if ($table) {

            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
            $idColumn = isset($indexes['primary']) ? (($primaryColumns = $indexes['primary']->getColumns()) && isset($primaryColumns[0]) ? $primaryColumns[0] : '') : '';

            if ($idColumn) {

                $columns = Schema::getColumnListing($table);
                $labelColumn = $labelColumn ? (in_array($labelColumn, $columns) ? $labelColumn : (($labelColumn = Str::snake($labelColumn)) && in_array($labelColumn, $columns) ? $labelColumn : '')) : $labelColumn;

                if (!$labelColumn) {

                    $nom = $name = $libelle = $label = null;

                    foreach ($columns as $column) {

                        if ($column != $idColumn) {

                            switch (Str::snake($column)) {

                                case 'nom' :

                                    $nom = $column;

                                break;

                                case 'name' :

                                    $name = $column;

                                break;

                                case 'libelle' :

                                    $libelle = $column;

                                break;

                                case 'label' :

                                    $label = $column;

                                break;
                            }
                        }
                    }

                    $labelColumn = $labelColumn ?: ($nom ?: ($name ?: ($libelle ?: ($label ?: $labelColumn))));
                }

                $colSelect = $labelColumn;
                $limit = $limit <= 0 ? 100 : $limit;

                if (!$labelColumn) {

                    $labelColumn = 'label';
                    $colSelect = DB::raw('CONCAT(\'' . addcslashes(Str::ucfirst(str_replace('_', ' ', Str::snake(Str::singular($table)))), '\'') . ' ' . '\', `' . $idColumn . '`)  AS `' . $labelColumn . '`');
                }

                $relations = DB::table($table)->select([$idColumn, $colSelect])->orderBy($labelColumn)->limit($limit)->get();

                if ($relations) {

                    $choices = $relations->pluck($labelColumn, $idColumn)->toArray();
                }
            }
        }

        return $choices;
    }
}
