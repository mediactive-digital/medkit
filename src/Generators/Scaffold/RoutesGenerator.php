<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use InfyOm\Generator\Generators\Scaffold\RoutesGenerator as InfyOmRoutesGenerator;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

use Str;

class RoutesGenerator extends InfyOmRoutesGenerator {

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
    private $routeContents;

    /** 
     * @var string 
     */
    private $routesTemplate;

    public function __construct(CommandData $commandData) {

        parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path');
        $this->routeContents = $this->getReflectionProperty('routeContents');

        $plural = Str::endsWith($this->commandData->modelName, 's');

        $this->routesTemplate = fill_template($this->commandData->dynamicVars, get_template('scaffold.routes.' . ($this->commandData->config->prefixes['route'] ? ($plural ? 'prefix_routes_plural' : 'prefix_routes') : ($plural ? 'routes_plural' : 'routes'))));
    }

    public function generate() {

        if (preg_match('/Route::[\s]*?resource[\s]*?\([\s]*?([\'|"])' . preg_quote($this->commandData->config->mSnakePlural, '/') . '\1/', $this->routeContents)) {

            $this->commandData->commandObj->info('Route ' . $this->commandData->config->mSnakePlural . ' already exists, Skipping Adjustment.');

            return;
        }

        file_put_contents($this->path, rtrim($this->routeContents) . $this->routesTemplate);
        $this->commandData->commandComment(FormatHelper::NEW_LINE . $this->commandData->config->mSnakePlural . ' routes added.');
    }

    public function rollback() {

        $pattern = preg_replace('/\s+/', '\s*', preg_quote($this->routesTemplate, '/'));
        $routeContents = preg_replace('/' . $pattern . '/', FormatHelper::NEW_LINE . FormatHelper::NEW_LINE, $this->routeContents, -1, $count);

        if ($count) {

            $routeContents = preg_replace('/\s+$/', '', $routeContents);
            $routeContents .= FormatHelper::NEW_LINE;

            file_put_contents($this->path, $routeContents);
            $this->commandData->commandComment('scaffold routes deleted');
        }
    }
}
