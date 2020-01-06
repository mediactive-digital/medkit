<?php

namespace MediactiveDigital\MedKit\Generators;

use InfyOm\Generator\Generators\MigrationGenerator as InfyOmMigrationGenerator;

class MigrationGenerator extends InfyOmMigrationGenerator {

    private function generateFields() {

        $fields = $foreignKeys = [];

        foreach ($this->commandData->fields as $field) {

            $fields[] = $field->migrationText;

            if (!empty($field->foreignKeyText)) {

                $foreignKeys[] = $field->foreignKeyText;
            }
        }

        return implode(infy_nl_tab(1, 3), array_merge($fields, $foreignKeys));
    }
}
