<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form as KrisForm;
use Kris\LaravelFormBuilder\Field;
use App\Models\Permission;
use App\Traits\Form;

class RoleForm extends KrisForm {

	use Form;

	public function buildForm() {

		$this->add('name', Field::TEXT, [
			'label'	 => _i('Nom'),
			'attr'	 => [
				'required'	 => 'required',
				'maxlength'	 => '255',
				'autofocus'	 => 'autofocus'
			]
		]);

		$this->add('guard_name', Field::TEXT, [
			'label'	 => _i('Guard name'),
			'attr'	 => [
				'required'	 => 'required',
				'maxlength'	 => '255'
			]
		]);

		
		$user	 = \Auth::user();
		if ($user->can('role-has-permissions_edit_all')) {
			$selectedValues = [];
			if ((bool) $this->model) {
				$selectedValues = $this->model->permissions()->pluck('id')->toArray();
			}

			$this->add('permissions', 'select2', [
				'choices'	 => Permission::all()->pluck('name', 'id')->toArray(),
				'selected'	 => $selectedValues
			]);
		}

		$this->add('submit', Field::BUTTON_SUBMIT, [
			'label'	 => _i('Enregistrer'),
			'attr'	 => [
				'class' => 'btn btn-primary btn-block'
			]
		]);
	}

}
