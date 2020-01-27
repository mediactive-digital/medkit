<?php

namespace MediactiveDigital\MedKit\Helpers;

use InfyOm\Generator\Common\GeneratorField;

use MediactiveDigital\MedKit\Common\CommandData;

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

    /** 
     * Check if field is JSON
     *
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return bool
     */
    public static function isJsonField(GeneratorField $field) {

        return in_array($field->htmlType, ['textarea', 'text']) && $field->fieldType == 'json';
    }

    /** 
     * Check if field is translatable
     *
     * @param \MediactiveDigital\MedKit\Common\CommandData $commandData
     * @param \InfyOm\Generator\Common\GeneratorField $field
     * @return bool
     */
    public static function isTranslatableField(GeneratorField $field, CommandData $commandData = null) {

        return ($commandData ? $commandData->getOption('translatable') : true) && 
            self::isJsonField($field) && 
            in_array(Str::snake($field->name), ['nom', 'name', 'libelle', 'label', 'nom_court', 'short_name', 'libelle_court', 'label_court', 'short_label', 'sujet', 'subject', 'template_html', 'html_template', 'template_texte', 'text_template']);
    }

    /**
     * Check if model has timestamps
     *
     * @param \MediactiveDigital\MedKit\Common\CommandData $commandData
     * @return bool $timestamps
     */
    public static function modelHasTimestamps(CommandData $commandData) {

        $timestamps = false;

        if ($commandData->timestamps) {

            foreach ($commandData->fields as $field) {

                if ($field->name == $commandData->timestamps[0]) {

                    $timestamps = true;

                    break;
                }
            }
        }

        return $timestamps;
    }

    /**
     * Check if model has soft delete
     *
     * @param \MediactiveDigital\MedKit\Common\CommandData $commandData
     * @return bool $softDelete
     */
    public static function modelHasSoftDelete(CommandData $commandData) {

        $softDelete = false;

        if ($commandData->getOption('softDelete') && $commandData->timestamps) {

            foreach ($commandData->fields as $field) {

                if ($field->name == $commandData->timestamps[2]) {

                    $softDelete = true;

                    break;
                }
            }
        }

        return $softDelete;
    }

    /**
     * Check if model has user stamps
     *
     * @param \MediactiveDigital\MedKit\Common\CommandData $commandData
     * @return bool $userStamps
     */
    public static function modelHasUserStamps(CommandData $commandData) {

        $userStamps = false;

        if ($commandData->getOption('userStamps') && $commandData->userStamps) {

            foreach ($commandData->fields as $field) {

                if ($field->name == $commandData->userStamps[0]) {

                    $userStamps = true;

                    break;
                }
            }
        }

        return $userStamps;
    }

    /** 
     * Check if model has translatable fields
     *
     * @param \MediactiveDigital\MedKit\Common\CommandData $commandData
     * @return bool $translatable
     */
    public static function modelHasTranslatable(CommandData $commandData) {

        $translatable = false;

        if ($commandData->getOption('translatable')) {

            foreach ($commandData->fields as $field) {

                if (self::isTranslatableField($field)) {

                    $translatable = true;

                    break;
                }
            }
        }

        return $translatable;
    }
}
