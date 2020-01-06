<?php

namespace MediactiveDigital\MedKit\Helpers;

use Schema;
use Str;

class Helper {

    /**
     * @var string
     */
    const AUTHOR = 'mediactive-digital';

    /**
     * @var string
     */
    const PACKAGE = 'medkit';

    /**
     * get path for stub file.
     *
     * @param string $stubName
     * @param string $stubType
     * @param string $stubsDir
     *
     * @return string $stubPath
     */
    public static function getTemplateFilePath(string $stubName, string $stubType = self::PACKAGE, string $stubsDir = '') {

        $stubName = str_replace('.', '/', $stubName);

        if (strpos($stubType, 'generator') !== false) {

            $stubType = self::PACKAGE;
        }
        else if (strpos($stubType, 'templates') !== false) {

            $stubType = FormatHelper::getTheme();
        }

        $vendorDir = 'vendor/' . self::AUTHOR . '/' . $stubType;
        $medkitStubsDir = self::AUTHOR . '/' . $stubType . '/stubs/';
        $medkitDir = $vendorDir . '/publishable/resources/' . $medkitStubsDir;
        $stubsDir = $stubsDir ?: config(self::AUTHOR . '.' . $stubType . '.path.stubs', resource_path($medkitStubsDir));

        $pathList = [
            $stubsDir . $stubName . '.stub',
            base_path($medkitDir . $stubName . '.stub'),
            base_path($vendorDir . '/stubs/' . $stubName . '.stub')
        ];

        foreach ($pathList as $path) {

            $stubPath = $path;

            if (file_exists($path)) {

                break;
            }
        }

        return $stubPath;
    }

    /**
     * get stub contents.
     *
     * @param string $stubName
     * @param string $stubType
     * @param string $stubsDir
     *
     * @return string
     */
    public static function getTemplate(string $stubName, string $stubType = self::PACKAGE, string $stubsDir = '') {

        $path = self::getTemplateFilePath($stubName, $stubType, $stubsDir);

        return file_get_contents($path);
    }

    /**
     * Get table name from assumed table name
     *
     * @param string $table
     * @return string $table
     */
    public static function getTableName(string $table): string {

        $table = Schema::hasTable($table) ? $table : (($table = Str::snake(Str::plural($table))) && Schema::hasTable($table) ? $table : '');

        if (!$table) {

            $classPrefix = '\\' . config('laravel_generator.namespace.model', 'App\Models') . '\\';
            $class = ($class = $classPrefix . $table) && class_exists($class) ? $class : (($class = $classPrefix . Str::studly(Str::singular($table))) && class_exists($class) ? $class : '');

            if ($class) {

                $table = (new $class)->getTable();
                $table = Schema::hasTable($table) ? $table : (($table = Str::snake(Str::plural($table))) && Schema::hasTable($table) ? $table : '');
            }
        }

        return $table;
    }

    /**
     * Get table primary key name
     *
     * @param string $table
     * @return string $primary
     */
    public static function getTablePrimaryName(string $table): string {

        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
        $primary = $indexes && isset($indexes['primary']) ? (($primaryColumns = $indexes['primary']->getColumns()) && isset($primaryColumns[0]) ? $primaryColumns[0] : '') : '';

        return $primary;
    }

    /**
     * Get table assumed label name
     *
     * @param string $table
     * @param string $label
     * @return string $label
     */
    public static function getTableLabelName(string $table, string $label = ''): string {

        $columns = Schema::getColumnListing($table);
        $label = $columns ? ($label ? (in_array($label, $columns) ? $label : (($label = Str::snake($label)) && in_array($label, $columns) ? $label : '')) : $label) : '';

        if (!$label) {

            $nom = $name = $libelle = $label = null;

            foreach ($columns as $column) {

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

            $label = $nom ?: ($name ?: ($libelle ?: ($label ?: '')));
        }

        return $label;
    }
}
