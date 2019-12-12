<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Traits\Request;

class UserRequest extends FormRequest {

    use Request;

    public function __construct() {

        $this->tableNameSingular = 'user';
    }

    /**
     * Set the validation rules that apply to the request.
     *
     * @return void
     */
    private function setRules() {

        $this->requestRules = [
            'name' => [
                'required',
                'max:255'
            ],
            'first_name' => [
                'required',
                'max:255'
            ],
            'email' => [
                'required',
                'max:255',
                'email',
                'unique:users,email,' . $this->modelId . ',id'
            ],
            'login' => [
                'required',
                'max:255',
                'unique:users,login,' . $this->modelId . ',id'
            ],
            'password' => [
                $this->setRule('required', 'nullable'),
                'min:8',
                'max:120',
                'confirmed',
                'regex:/^.*(?=.{8,120})(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])(?=.*[^a-zA-Z\d\s]).*$/'
            ],
            'theme' => [
                'boolean'
            ]
        ];
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return void
     */
    private function setMessages() {

        $this->requestMessages = [
            'password.regex' => _i('Le mot de passe doit contenir au minimum : une majuscule, une minuscule, un chiffre et un caractère spécial.')
        ];
    }
}
