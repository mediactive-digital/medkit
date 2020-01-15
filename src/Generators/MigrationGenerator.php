<?php

namespace MediactiveDigital\MedKit\Generators;

use InfyOm\Generator\Generators\MigrationGenerator as InfyOmMigrationGenerator;
use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;

use File;
use Str;

class MigrationGenerator extends InfyOmMigrationGenerator {

    use Reflection;

    /** 
     * @var CommandData 
     */
    private $commandData;

    /** 
     * @var string 
     */
    private $path;

    /** 
     * @var string 
     */
    private $fileName;

    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path');
        $this->fileName = 'create_' . strtolower($this->commandData->dynamicVars['$TABLE_NAME$']) . '_table.php';
    }

    public function generate() {

        $templateData = get_template('migration');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);
        $templateData = str_replace('$FIELDS$', $this->callReflectionMethod('generateFields'), $templateData);

        $fileName = $this->getExistingFile() ?: date('Y_m_d_His') . '_' . $this->fileName;
        FileUtil::createFile($this->path, $fileName, $templateData);

        $this->commandData->commandComment("\nMigration created: ");
        $this->commandData->commandInfo($fileName);
    }

    public function rollback() {

        if (($file = $this->getExistingFile()) && $this->rollbackFile($this->path, $file)) {

            $this->commandData->commandComment('Migration file deleted: ' . $file);
        }
    }

    /**
     * Get existing migration files list
     *
     * @return string $existingFile
     */
    public function getExistingFile() {

        $existingFile = '';
        $allFiles = File::allFiles($this->path);
        $files = [];

        foreach ($allFiles as $file) {

            $files[] = $file->getFilename();
        }

        $files = array_reverse($files);

        foreach ($files as $file) {

            if (Str::endsWith($file, $this->fileName)) {

                $existingFile = $file;

                break;
            }
        }

        return $existingFile;
    }
}
