<?php

use MediactiveDigital\MedKit\Helpers\Helper;

if (!function_exists('get_template_file_path')) {

    /**
     * get path for stub file.
     *
     * @param string $stubName
     * @param string $stubType
     * @param string $stubsDir
     *
     * @return string
     */
    function get_template_file_path(string $stubName, string $stubType = Helper::PACKAGE, string $stubsDir = '') {

        return Helper::getTemplateFilePath($stubName, $stubType, $stubsDir);
    }
}

if (!function_exists('get_template')) {

    /**
     * get stub contents.
     *
     * @param string $stubName
     * @param string $stubType
     * @param string $stubsDir
     *
     * @return string
     */
    function get_template(string $stubName, string $stubType = Helper::PACKAGE, string $stubsDir = '') {

        return Helper::getTemplate($stubName, $stubType, $stubsDir);
    }
}
