<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use InfyOm\Generator\Utils\FileUtil;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Generators\Scaffold\PermissionGenerator;
use MediactiveDigital\MedKit\Helpers\FormatHelper;

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
	public function rollback() {

		if ($this->rollbackFile($this->path, $this->fileName)) {

			$this->commandData->commandComment('Policy file deleted: ' . $this->fileName);
		}
	}

}
