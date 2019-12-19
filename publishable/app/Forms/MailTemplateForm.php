<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form as KrisForm;
use Kris\LaravelFormBuilder\Field;

use App\Traits\Form;

class MailTemplateForm extends KrisForm {

	use Form;

    public function buildForm() {

        $this->add('mailable', Field::TEXT, [
                'label' => _i('Mailable'),
                'attr' => [
                    'required' => 'required',
                    'maxlength' => '255',
                    'autofocus' => 'autofocus'
                ]
            ]);

        $this->add('subject', Field::TEXTAREA, [
                'label' => _i('Subject')
            ]);

        $this->add('html_template', Field::TEXTAREA, [
                'label' => _i('Html template'),
                'attr' => [
                    'required' => 'required'
                ]
            ]);

        $this->add('text_template', Field::TEXTAREA, [
                'label' => _i('Text template')
            ]);

        $this->add('submit', Field::BUTTON_SUBMIT, [
                'label' => _i('Enregistrer'),
                'attr' => [
                    'class' => 'btn btn-primary btn-block'
                ]
            ]);
    }
}
