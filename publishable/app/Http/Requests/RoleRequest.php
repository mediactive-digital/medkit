<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Traits\Request;

class RoleRequest extends FormRequest {

    use Request;

    public function __construct() {

        $this->tableNameSingular = 'role';
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
            'guard_name' => [
                'required',
                'max:255'
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
        ];
    }
}
