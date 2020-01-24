<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use InfyOm\Generator\Generators\Scaffold\MenuGenerator as InfyOmMenuGenerator;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

class MenuGenerator extends InfyOmMenuGenerator {

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
    private $menuContents;

    /** 
     * @var string 
     */
    private $menuTemplate;

	/**
	 * 
	 * @param CommandData $commandData
	 */
    public function __construct(CommandData $commandData) {

        $templateName = 'menu';

		if ( config('infyom.laravel_generator.add_on.permissions.enabled', true) && config('infyom.laravel_generator.add_on.permissions.policies', true) ) { 
				$templateName .= '_policies';
		}
		
        $this->commandData = $commandData;
        $this->path = $this->commandData->config->pathMiddlewares . $commandData->getAddOn('menu.menu_file');
        $this->menuContents = file_get_contents($this->path);
        $this->menuTemplate = get_template('scaffold.menu.' . $templateName);
        $this->menuTemplate = fill_template($this->commandData->dynamicVars, $this->menuTemplate);

        $this->setMenuConfiguration();
    }

    /** 
     * Set configuration for menu generation
     *
     * @return void 
     */
    private function setMenuConfiguration() {

        $this->setReflectionProperty('commandData', $this->commandData);
        $this->setReflectionProperty('path', $this->path);
        $this->setReflectionProperty('menuContents', $this->menuContents);
        $this->setReflectionProperty('menuTemplate', $this->menuTemplate);
    }

    public function generate() {

        $add = false;

        $this->menuContents = preg_replace_callback('/(backoffice[\s\S]+Menu::make[\s\S]+?{)([\s\S]*?)(\$menu->sortBy)/', function($matches) use (&$add) {

            if (strpos($matches[2], $this->commandData->config->prefixes['route'] . '.' . $this->commandData->config->mCamelPlural . '.index') !== false) {

                $return = $matches[1] . $matches[2] . $matches[3];
            }
            else {

                $return = $matches[1] . rtrim($matches[2]) . $this->menuTemplate . $matches[3];

                $add = true;
            }
    
            return $return;

        }, $this->menuContents);

        if ($add) {

            $this->commandData->commandComment("\n" . $this->commandData->config->mCamelPlural . ' menu added.');

            file_put_contents($this->path, $this->menuContents);
        }
        else {

            $this->commandData->commandObj->info('Menu ' . $this->commandData->config->mHumanPlural . ' already exists, Skipping Adjustment.');
        }
    }

    public function rollback() {

        $pattern = preg_replace('/\s+/', '\s*', preg_quote($this->menuTemplate, '/'));
        $menuContents = preg_replace('/' . $pattern . '/', FormatHelper::NEW_LINE . FormatHelper::NEW_LINE, $this->menuContents, -1, $count);

        if ($count) {

            file_put_contents($this->path, $menuContents);
            $this->commandData->commandComment('menu deleted');
        }
    }
}
