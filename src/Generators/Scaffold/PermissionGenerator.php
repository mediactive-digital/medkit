<?php

namespace MediactiveDigital\MedKit\Generators\Scaffold;

// use InfyOm\Generator\Generators\Scaffold\MenuGenerator as InfyOmMenuGenerator;

use MediactiveDigital\MedKit\Common\CommandData;
use MediactiveDigital\MedKit\Traits\Reflection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Str;
use InfyOm\Generator\Generators\BaseGenerator;
use Illuminate\Support\Facades\DB;

class PermissionGenerator extends BaseGenerator {

	use Reflection;

	/**
	 * @var CommandData
	 */
	private $commandData;
	
	private $idRoleSuperAdmin;
	
	public $permissionsAbilityCrudOwn = [
		'edit_own',
		'delete_own',
		'view_own',
	];
	
	public $permissionsAbilityCrudDefault = [
		'create',
		'edit_all',
		'delete_any',
		'view_all'
	];

	public function __construct(CommandData $commandData) {

		$this->commandData		 = $commandData;
		$this->idRoleSuperAdmin	 = config('infyom.laravel_generator.add_on.permissions.superadmin_role_id', 1);
	}

	/**
	 *
	 * @return boolean
	 */
	public function isCreateByExist() {
		$isExist	 = false;
		$tableName	 = $this->commandData->dynamicVars['$TABLE_NAME$'];
		$champName	 = config('infyom.laravel_generator.add_on.user_stamps.created_by', 1);
		$columns	 = DB::select("SHOW COLUMNS FROM " . $tableName . " LIKE '" . $champName . "'");

		if (is_array($columns) && count($columns) > 0) {

			$isExist = true;
		}

		return $isExist;
	}

	/**
	 *
	 * @return array
	 */
	public function getPermissionsAbility() {
		$aAbility	 = array();
		$aAbility	 += $this->permissionsAbilityCrudDefault;

		// on met les own
		if ($this->isCreateByExist()) {
			$aAbility += $this->permissionsAbilityCrudOwn;
		}
 
		return $aAbility;
	}

	/**
	 * 
	 */
	public function generate() {

		$add = true;

		if ($add) {
			$this->commandData->commandObj->info("\n" . 'Generating & assign ' . $this->commandData->config->mHumanPlural);

			$role = Role::find($this->idRoleSuperAdmin); 
			foreach ($this->getPermissionsAbility() as $valuePermission) {

				// existe deja
				$permissionName	 = $this->commandData->config->mDashedPlural . '_' . $valuePermission;
				$permission		 = Permission::where('name', $permissionName);

				$infoLabel = 'Permission ' . $permissionName . ' already exists, Skipping Adjustment.';
				if (count($permission->get()) < 1) {
					$permission	 = Permission::create(['name' => $permissionName]);
					$infoLabel	 = 'Permission ' . $permissionName . ' added.';
				}
				$this->commandData->commandComment("\n" . $infoLabel);

				$infoLabel = 'Permission ' . $permissionName . ' already assign to <'. $role->name.'>, Skipping Adjustment.';
				if (!$role->hasPermissionTo($permissionName)) {
					// $permission->assignRole($role);
					$role->givePermissionTo($permission);
					$infoLabel = 'Permission ' . $permissionName . ' assign to <'. $role->name.'> added.';
				}

				$this->commandData->commandComment($infoLabel);
			}
		} else {

			$this->commandData->commandObj->info('Permissions ' . $this->commandData->config->mHumanPlural . ' already exists, Skipping Adjustment.');
		}
	}

	public function rollback() {
		// todo
		$role = Role::find($this->idRoleSuperAdmin);
		foreach ($this->getPermissionsAbility() as $valuePermission) {
			// existe deja 
			$permissionName	 = $this->commandData->config->mDashedPlural . '_' . $valuePermission;
			$permission = Permission::findByName($permissionName);
			$role->revokePermissionTo($permission);
			Permission::destroy($permission->id);
		}

		$this->commandData->commandComment('Permissions deleted & unassign to <'. $role->name.'>');
	}

}
