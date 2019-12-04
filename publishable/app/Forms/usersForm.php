<?php

namespace App\Forms;

use App\Models\Role;
use Kris\LaravelFormBuilder\Form as KrisForm;
use Kris\LaravelFormBuilder\Field;

use App\Traits\Form;

class usersForm extends KrisForm {

	use Form;

    public function buildForm() {

        $this->add('name', Field::TEXT, [
                'label' => _i('Name'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255',
                    'autofocus' => 'autofocus'
                ]
            ]);

        $this->add('firstname', Field::TEXT, [
                'label' => _i('Firstname'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255'
                ]
            ]);

        $this->add('email', Field::EMAIL, [
                'label' => _i('Email'),
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
                    'label' => _i('Password'),
                    'attr' => [
                        'required' => $this->setAttribute('required', false),
                        'minlength' => '8',
                        'maxlength' => '120'
                    ],
                    'value' => $this->formatNull()
                ],
                'second_options' => [
                    'label' => _i('Password confirmation'),
                    'attr' => [
                        'required' => $this->setAttribute('required', false),
                        'minlength' => '8',
                        'maxlength' => '120'
                    ]
                ]
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
