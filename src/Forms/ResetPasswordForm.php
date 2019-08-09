<?php

namespace MediactiveDigital\MedKit\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class ResetPasswordForm extends Form {

	public function buildForm() {

		$this->add('token', Field::HIDDEN, [
				'value' => $this->getData('token')
			])
			->add('email', Field::EMAIL, [
				'label' => _i('Email'),
				'attr' => [
					'readonly' => 'readonly',
					'required' => 'required'
				],
				'value' => $this->getData('email')
			])
			->add('password', 'repeated', [
				'type' => Field::PASSWORD,
				'second_name' => 'password_confirmation',
			    'first_options' => [
			    	'label' => _i('Mot de passe'),
					'attr' => [ 
						'autofocus' => 'autofocus',
						'required' => 'required'
					],
					'help_block' => [
						'text' => _i('%d caractères minimum', 8),
						'tag' => 'p',
						'attr' => [
							'class' => 'help-block'
						]
					]
			    ],
			    'second_options' => [
				    'label' => _i('Confirmer le mot de passe'),
					'attr' => [ 
						'required' => 'required'
					]
				]
			])
			->add('submit', Field::BUTTON_SUBMIT, [
				'label' => _i('Réinitialiser le mot de passe'),
				'attr' => [
					'class' => 'btn btn-primary btn-block'
				]
			]);
		
	}
}
