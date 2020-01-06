<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form as KrisForm;
use Kris\LaravelFormBuilder\Field;

use App\Traits\Form;

class PermissionForm extends KrisForm {

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

        $this->add('guard_name', Field::TEXT, [
                'label' => _i('Guard name'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255'
                ]
            ]);

        $this->add('submit', Field::BUTTON_SUBMIT, [
                'label' => _i('Enregistrer'),
                'attr' => [
                    'class' => 'btn btn-primary btn-block'
                ]
            ]);
    }
}
