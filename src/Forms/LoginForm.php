<?php

namespace MediactiveDigital\MedKit\Forms;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class LoginForm extends Form {

	public function buildForm() {

		$this->add('login', Field::TEXT, [
				'label' => _i('Identifiant'),
				'attr' => [
					'autofocus' => 'autofocus',
					'required' => 'required'
				]
			])
			->add('password', Field::PASSWORD, [
				'label' => _i('Mot de passe'),
				'attr' => [ 
					'required' => 'required'
				]
			])
			->add('remember', Field::CHECKBOX, [
				'label' => _i('Se souvenir de moi')
			])
			->add('submit', Field::BUTTON_SUBMIT, [
				'label' => _i('Se connecter'),
				'attr' => [
					'class' => 'btn btn-primary btn-block'
				]
			]);
	}
}
