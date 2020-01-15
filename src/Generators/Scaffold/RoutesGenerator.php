<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use InfyOm\Generator\Generators\Scaffold\RoutesGenerator as InfyOmRoutesGenerator;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;

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
        $this->routesTemplate = $this->getReflectionProperty('routesTemplate');
    }

    public function generate() {

        if (Str::contains($this->routeContents, 'Route::resource(\'' . $this->commandData->config->mSnakePlural . '\',')) {

            $this->commandData->commandObj->info('Route ' . $this->commandData->config->mSnakePlural . ' already exists, Skipping Adjustment.');

            return;
        }

        file_put_contents($this->path, rtrim($this->routeContents) . $this->routesTemplate);
        $this->commandData->commandComment("\n" . $this->commandData->config->mSnakePlural . ' routes added.');
    }
}
