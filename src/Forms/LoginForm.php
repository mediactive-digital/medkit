<?php

namespace MediactiveDigital\MedKit\Forms;

use Kris\LaravelFormBuilder\Form;

class LoginForm extends Form {

	public function buildForm() {
		$this
			->add('login', 'text', [
				'label' => _i('Identifiant'),
				'attr' => [
					'autofocus' => 'autofocus',
					'required' => 'required'
				],
			])
			->add('password', 'password', [
				'label' => _i('Mot de passe'),
				'attr' => [ 
					'required' => 'required'
				],
			])
			->add('remember', 'checkbox', [
				'label' => _i('Se souvenir de moi')
			])
			->add('submit', 'submit', [
				'label'		 => _i('Se connecter'),
				//'attr' => ['class' => 'btn-primary'],
				'help_block' => [
					'text'	 => '<a class="ml-1" href="' . route('back.password.request') . '">' . _i('Mot de passe oublié') . ' </a>',
					'tag'	 => 'p',
					'attr'	 => ['class' => 'help-block']
				],
		]);
	}

}
