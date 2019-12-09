<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InfyOm\Generator\Generators\BaseGenerator;
use InfyOm\Generator\Utils\FileUtil;
use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use MediactiveDigital\MedKit\Generators\Scaffold\PermissionGenerator;

class PolicyGenerator extends PermissionGenerator {

	use Reflection;

	/** @var CommandData */
	private $commandData;

	/** @var string */
	private $path;

	/**
	 * @var string
	 */
	private $fileName;

	public function __construct(CommandData $commandData) {
		$this->commandData = $commandData;

		$this->path = config('infyom.laravel_generator.path.policies', app_path('Policies/'));

		$this->fileName = $this->commandData->modelName . 'Policy.php';

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

		$champCreatedByName = config('infyom.laravel_generator.add_on.user_stamps.created_by', 1);
		$this->commandData->addDynamicVariable('$BD_FIELD_CREATED_BY_NAME$', $champCreatedByName);
	}

	/**
	 *
	 */
	public function generatePolicy() {

		$templateData	 = get_template('scaffold.policy.policy');
		$templateData	 = fill_template($this->commandData->dynamicVars, $templateData);

		$templateData	 = str_replace('$RULE_VIEW_ANY_FOR_OWN$', FormatHelper::writeValueToPhp($this->generateViewAnyforOwn(), 2), $templateData);
		$templateData	 = str_replace('$RULE_VIEW_FOR_OWN$', FormatHelper::writeValueToPhp($this->generateViewForOwn(), 2), $templateData);
		$templateData	 = str_replace('$RULE_UPDATE_FOR_OWN$', FormatHelper::writeValueToPhp($this->generateUpdateForOwn(), 2), $templateData);
		$templateData	 = str_replace('$RULE_DELETE_FOR_OWN$', FormatHelper::writeValueToPhp($this->generateDeleteForOwn(), 2), $templateData);

		FileUtil::createFile($this->path, $this->fileName, $templateData);

		$this->commandData->commandComment("\nBase Policy created: ");
		$this->commandData->commandInfo($this->fileName);
	}

	/**
	 *
	 */
	public function generateViewAnyForOwn() {

		if ($this->isCreateByExist()) {

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


		if ($this->isCreateByExist()) {

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


		if ($this->isCreateByExist()) {

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


		if ($this->isCreateByExist()) {

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
