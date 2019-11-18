<?php

use MediactiveDigital\MedKit\Helpers\FormatHelper;

if (!function_exists('get_template_file_path')) {

    /**
     * get path for stub file.
     *
     * @param string $stubName
     * @param string $stubType
     * @param string $stubsDir
     *
     * @return string $stubPath
     */
    function get_template_file_path(string $stubName, string $stubType = '', string $stubsDir = '') {

        $author = 'mediactive-digital';
        $package = 'medkit';
        $stubName = str_replace('.', '/', $stubName);

        if (strpos($stubType, 'generator') !== false) {

            $stubType = $package;
        }
        else if (strpos($stubType, 'templates') !== false) {

            $stubType = FormatHelper::getTheme();
        }

        $stubType = $stubType ?: $package;
        $vendorDir = 'vendor/' . $author . '/' . $stubType;
        $medkitStubsDir = $author . '/' . $stubType . '/stubs/';
        $medkitDir = $vendorDir . '/publishable/resources/' . $medkitStubsDir;
        $stubsDir = $stubsDir ?: config($author . '.' . $stubType . '.path.stubs', resource_path($medkitStubsDir));

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
    function get_template(string $stubName, string $stubType = '', string $stubsDir = '') {

        $path = get_template_file_path($stubName, $stubType, $stubsDir);

        return file_get_contents($path);
    }
}
