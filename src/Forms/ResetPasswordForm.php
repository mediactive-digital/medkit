<?php

namespace MedKit\Forms;

use Kris\LaravelFormBuilder\Form;

class ResetPasswordForm extends Form {

	public function buildForm() {
		$this
			->add('email', 'email', [
				'label'	 => _i('Email'),
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
