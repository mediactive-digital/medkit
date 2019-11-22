<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use InfyOm\Generator\Generators\Scaffold\RequestGenerator as InfyOmRequestGenerator;
use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;

use Str;

class RequestGenerator extends InfyOmRequestGenerator {

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
    private $createFileName;

    /** 
     * @var string 
     */
    private $updateFileName;

    public function __construct(CommandData $commandData) {

    	parent::__construct($commandData);

        $this->commandData = $commandData;
        $this->path = $this->getReflectionProperty('path');
        $this->createFileName = $this->getReflectionProperty('createFileName');
        $this->updateFileName = $this->getReflectionProperty('updateFileName');
    }

    private function generateUpdateRequest() {

        $this->commandData->addDynamicVariable('$TABLE_NAME_SINGULAR$', Str::singular($this->commandData->config->tableName));

        $templateData = get_template('scaffold.request.update_request');
        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, $this->updateFileName, $templateData);

        $this->commandData->commandComment("\nUpdate Request created: ");
        $this->commandData->commandInfo($this->updateFileName);
    }
}
