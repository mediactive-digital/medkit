<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Generators\Scaffold\PermissionGenerator;

use Str;

class PolicyGenerator extends PermissionGenerator {

	use Reflection;

	/** @var CommandData */
	private $commandData;

	/**
	 * @var array
	 */
	private $userStamps;

	/** @var string */
	private $path;
	private $templateType;
	private $fileName;

    private $providerPath;
	private $providerContents; 
    private $providerTemplate;
	
	/** @var boolean */
	private $optionUserStamps;

	
	public function __construct(CommandData $commandData) {

		parent::__construct($commandData);

		$this->optionUserStamps	 = $this->getReflectionProperty('optionUserStamps');
		$this->userStamps		 = $this->getReflectionProperty('userStamps');

		$this->commandData = $commandData;

		$this->path = config('infyom.laravel_generator.path.policies', app_path('Policies/'));

		$this->fileName = $this->commandData->modelName . 'Policy.php';

		$this->templateType = 'medkit';

		
        $this->providerPath = config('infyom.laravel_generator.path.auth_provider');
        $this->providerContents = file_get_contents($this->providerPath); 
        $this->providerTemplate = get_template('scaffold.policy.provider');
        $this->providerTemplate = fill_template($this->commandData->dynamicVars, $this->providerTemplate);
		 
		$this->setRequestConfiguration();
	}

	/**
	 * Set configuration for request generation
	 *
	 * @return void
	 */
	private function setRequestConfiguration() {

		/**
		 * On cree les clefs stubs pour les permissions
		 * $EDIT_OWN$
		 * $DELETE_OWN$
		 * $VIEW_OWN$
		 * $CREATE$
		 * $EDIT_ALL$
		 * $DELETE_ANY$
		 * $VIEW_ALL$
		 */
		foreach ($this->getPermissionsAbility() as $valuePermission) {
			$permissionName = $this->commandData->config->mDashedPlural . '_' . $valuePermission;
			$this->commandData->addDynamicVariable('$' . strtoupper($valuePermission) . '$', $permissionName);
		}

		$champCreatedByName = config('infyom.laravel_generator.user_stamps.created_by', 'created_by');
		$this->commandData->addDynamicVariable('$BD_FIELD_CREATED_BY_NAME$', $champCreatedByName);

		$namespaceName = config('infyom.laravel_generator.namespace.policies', 'App\Policies');
		$this->commandData->addDynamicVariable('$NAMESPACE_POLICIES$', $namespaceName);
	}

	/**
	 * 
	 */
    public function generateProvider() {

        $add = false;

        $this->providerContents = preg_replace_callback('/(# policiesGenerator)'
			. '([\s\S]*?)'
			. '(# fin policiesGenerator)/', function($matches) use (&$add) {
 
            if (strpos($matches[2],  ucfirst($this->commandData->config->mCamel) . '::class' ) !== false) {

                $return = $matches[1] . $matches[2] . $matches[3];
            }
            else {

                $return = $matches[1] . rtrim($matches[2]) . $this->providerTemplate . $matches[3];

                $add = true;
            }
  
            return $return;

        }, $this->providerContents);

        if ($add) {

            $this->commandData->commandComment("\n" . $this->commandData->config->mCamelPlural . ' policy provider added.');

            file_put_contents($this->path, $this->providerContents);
        }
        else {

            $this->commandData->commandObj->info('Policy provider ' . $this->commandData->config->mHumanPlural . ' already exists, Skipping Adjustment.');
        }
    }
	
	/**
	 *
	 */
	public function generatePolicy() {

		$templateData = get_template('scaffold.policy.policy');

		$templateData	 = str_replace('$RULE_VIEW_ANY_FOR_OWN$', $this->generateViewAnyforOwn(), $templateData);
		$templateData	 = str_replace('$RULE_VIEW_FOR_OWN$', $this->generateViewForOwn(), $templateData);
		$templateData	 = str_replace('$RULE_UPDATE_FOR_OWN$', $this->generateUpdateForOwn(), $templateData);
		$templateData	 = str_replace('$RULE_DELETE_FOR_OWN$', $this->generateDeleteForOwn(), $templateData);

		$templateData = fill_template($this->commandData->dynamicVars, $templateData);

		FileUtil::createFile($this->path, $this->fileName, $templateData);

		$this->commandData->commandComment("\nBase Policy created: ");
		$this->commandData->commandInfo($this->fileName);
	}

	/**
	 *
	 */
	public function generateViewAnyForOwn() {

		if ($this->hasUserStamps()) {

			$templateData = get_template('scaffold.policy.view_any_for_own', $this->templateType);

			return fill_template($this->commandData->dynamicVars, $templateData);
		} else {
			return "";
		}
	}

	/**
	 *
	 */
	public function generateViewForOwn() {

		if ($this->hasUserStamps()) {

			$templateData = get_template('scaffold.policy.view_for_own', $this->templateType);

			return fill_template($this->commandData->dynamicVars, $templateData);
		} else {
			return "";
		}
	}

	/**
	 *
	 */
	public function generateUpdateForOwn() {

		if ($this->hasUserStamps()) {

			$templateData = get_template('scaffold.policy.update_for_own', $this->templateType);

			return fill_template($this->commandData->dynamicVars, $templateData);
		} else {
			return "";
		}
	}

	/**
	 *
	 */
	public function generateDeleteForOwn() {

		if ($this->hasUserStamps()) {

			$templateData = get_template('scaffold.policy.delete_for_own', $this->templateType);

			return fill_template($this->commandData->dynamicVars, $templateData);
		} else {
			return "";
		}
	}

	/**
	 *
	 */
	public function rollbackPolicy() {

		if ($this->rollbackFile($this->path, $this->fileName)) {

			$this->commandData->commandComment('Policy file deleted: ' . $this->fileName);
		}
	}

	
    public function rollbackProvider( )
    {   
        if (Str::contains($this->providerTemplate, $this->providerTemplate)) {
            file_put_contents($this->providerPath, str_replace($this->providerTemplate, '', $this->providerTemplate));
            $this->commandData->commandComment('Tracker history  deleted');
        } 
    }
	
	
	
	/**
	 *
	 */
	public function rollback() {

		$this->rollbackPolicy( );
		$this->rollbackProvider( );
	}

}
