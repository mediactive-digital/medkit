<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form as KrisForm;
use Kris\LaravelFormBuilder\Field;

use App\Models\Permission;

use App\Traits\Form;

use Auth;

class RoleForm extends KrisForm {

	use Form;

	public function buildForm() {

		$user = Auth::user();

		$this->add('name', Field::TEXT, [
			'label'	=> _i('Nom'),
			'attr' => [
				'required' => 'required',
				'maxlength'	=> '255',
				'autofocus'	=> 'autofocus'
			]
		]);

		$this->add('guard_name', Field::TEXT, [
			'label'	=> _i('Guard name'),
			'attr' => [
				'required' => 'required',
				'maxlength'	=> '255'
			]
		]);

		
		if ($user->can('role-has-permissions_edit_all')) {

			$this->add('permissions', 'select2', [
				'choices' => Permission::all()->pluck('name', 'id')->toArray()
			]);
		}

		$this->add('submit', Field::BUTTON_SUBMIT, [
			'label'	=> _i('Enregistrer'),
			'attr' => [
				'class' => 'btn btn-primary btn-block'
			]
		]);
	}
}
