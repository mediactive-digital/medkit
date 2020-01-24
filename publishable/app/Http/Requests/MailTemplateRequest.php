<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Traits\Request;

class MailTemplateRequest extends FormRequest {

    use Request;

    public function __construct() {

        $this->tableNameSingular = 'mail_template';
    }

    /**
     * Set the validation rules that apply to the request.
     *
     * @return void
     */
    private function setRules() {

        $this->requestRules = [
            'mailable' => [
                'required',
                'max:255'
            ],
            'subject' => [
                'array'
            ],
            'html_template' => [
                'required',
                'array'
            ],
            'text_template' => [
                'array'
            ]
        ];
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return void
     */
    private function setMessages() {

        $this->requestMessages = [];
    }
}
