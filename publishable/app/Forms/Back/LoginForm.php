<?php

namespace App\Forms\Back;

use MediactiveDigital\MedKit\Forms\LoginForm as MedKitLoginForm;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class LoginForm extends Form {

    public function buildForm() {
        // $this->remove('login');
        // dd($this);
        // $the_form = MedKitLoginForm::buildForm();
         //dd(  $the_form );
        // dd($the_form);

        // $the_form->remove('login');
        $this->add('login', 'inputgroup', [
            'icon' => [
                'name' => 'person',
                'prepend' => true,
                'append' => false,
                'wrapper' => 'bg-warning',
                'class' => 'material-icons'
            ],
            'label'	=> _i('Identifiant'),
            'label_show' => false,
            'attr' => [
                'type' => Field::TEXT,
                'autofocus' => 'autofocus',
                'required' => 'required',
                'placeholder' => _i('Identifiant')
            ]
        ])->add('password', 'inputgroup', [
            'icon' => [
                'name' => 'lock',
                'prepend' => true,
                'append' => false,
                'wrapper' => 'bg-warning',
                'class' => 'material-icons'
            ],
            'label' => _i('Mot de passe'),
            'label_show' => false,
            'attr' => [ 
                'type' => Field::PASSWORD,
                'required' => 'required',
                'placeholder' => _i('Mot de passe')
            ]
        ])
        ->add('remember', 'onecustomcheckbox', [
            'label' => _i('Se souvenir de moi')
        ])
        ->add('submit', Field::BUTTON_SUBMIT, [
            'label' => _i('Se connecter'),
            'attr' => [
                'class' => 'btn btn-primary btn-block mt-3'
            ]
        ]);
        
        // dd($the_form);
                
        // return parent::buildForm();
    } 
}
