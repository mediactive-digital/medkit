<?php

namespace MediactiveDigital\MedKit\Forms;

use Kris\LaravelFormBuilder\Form;

class ResetPasswordForm extends Form {

	public function buildForm() {
		$this
			->add('password', 'text', [
				'label'	 => _i('TODO'),
				'attr'	 => [
					'autofocus'	 => 'autofocus',
					'required'	 => 'required'
				],
			])
			->add('submit', 'submit', [
				'label' => _i('Valider'),
			]);
		
	}

}
