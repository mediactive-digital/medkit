<?php

namespace MediactiveDigital\MedKit\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class ForgotPasswordForm extends Form {

	public function buildForm() {

		$this->add('email', Field::EMAIL, [
				'label'	=> _i('Email'),
				'attr' => [
					'autofocus'	=> 'autofocus',
					'required' => 'required'
				]
			])
			->add('submit', Field::BUTTON_SUBMIT, [
				'label' => _i('Valider'),
				'attr' => [
					'class' => 'btn btn-primary btn-block'
				]
			]);
		
	}
}
