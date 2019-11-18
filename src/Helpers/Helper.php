<?php

namespace MediactiveDigital\MedKit\Helpers;

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
}
