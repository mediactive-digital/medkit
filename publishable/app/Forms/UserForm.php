<?php

namespace App\Forms;

use App\Models\Role;
use Kris\LaravelFormBuilder\Form as KrisForm;
use Kris\LaravelFormBuilder\Field;

use App\Traits\Form;

class UserForm extends KrisForm {

	use Form;

    public function buildForm() {

        $this->add('name', Field::TEXT, [
                'label' => _i('Nom'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255',
                    'autofocus' => 'autofocus'
                ]
            ]);

        $this->add('first_name', Field::TEXT, [
                'label' => _i('Prénom'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255'
                ]
            ]);

        $this->add('email', Field::EMAIL, [
                'label' => _i('Adresse email'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255'
                ]
            ]);

        $this->add('login', Field::TEXT, [
                'label' => _i('Login'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255'
                ]
            ]);

        $this->add('password', 'repeated', [
                'type' => Field::PASSWORD,
                'second_name' => 'password_confirmation',
                'first_options' => [
                    'label' => _i('Mot de passe'),
                    'attr' => [
                        'required' => $this->setAttribute('required', false),
                        'minlength' => '8',
                        'maxlength' => '120'
                    ],
                    'value' => $this->formatNull()
                ],
                'second_options' => [
                    'label' => _i('Confirmation du mot de passe'),
                    'attr' => [
                        'required' => $this->setAttribute('required', false),
                        'minlength' => '8',
                        'maxlength' => '120'
                    ]
                ]
            ]);

        $this->add('theme', Field::CHECKBOX, [
                'label' => _i('Theme'),
                'value' => 1
            ]);

        $selectedValues = [];
        if((bool) $this->model) {
            $selectedValues = $this->model->roles()->pluck('id')->toArray();
        }

        $this->add('roles','select2',[
            'choices' => Role::all()->pluck('name','id')->toArray(),
            'selected' => $selectedValues
        ]);
		
        $this->add('submit', Field::BUTTON_SUBMIT, [
                'label' => _i('Enregistrer'),
                'attr' => [
                    'class' => 'btn btn-primary btn-block'
                ]
            ]);
    }
}
 