<?php

namespace MediactiveDigital\MedKit\Traits;

use Str;

trait Request {

    /** 
     * @var mixed $modelInstance
     */
    private $modelInstance;

    /** 
     * @var bool $isUpdate
     */
    private $isUpdate;

    /** 
     * @var array $rules
     */
    private $rules;

    /** 
     * @var array $messages
     */
    private $messages;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $this->getRules();
        $this->formatRules();

        return $this->rules;
    }

    /**
     * Get validation rules
     *
     * @return array
     */
    private function getRules() {

        $this->rules = [];

        return $this->rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {

        $this->getMessages();

        return $this->messages;
    }

    /**
     * Get validation messages
     *
     * @return array
     */
    private function getMessages() {

        $this->messages = [];

        return $this->messages;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() {

        $this->formatDatas();
    }

    /**
     * Format rules.
     *
     * @return void
     */
    private function formatRules() {

        foreach ($this->rules as $key => $rules) {

            foreach ($rules as $index => $rule) {

                $this->formatRequiredRule($key, $index);
            }
        }
    }

    /**
     * Format required rule.
     *
     * @param string $key
     * @param int $index
     * @return void
     */
    private function formatRequiredRule(string $key, int $index) {

        if ($this->rules[$key][$index] == 'required') {

            if ($this->isUpdate) {

                if (Str::contains(Str::lower($key), 'password')) {

                    $this->rules[$key][$index] = 'nullable';
                }
            }
        }
    }

    /**
     * Format request datas.
     *
     * @return void
     */
    private function formatDatas() {

        foreach ($this->all() as $key => $value) {

            $this->formatPasswordData($key);
        }
    }

    /**
     * Format password request data.
     *
     * @param string $key
     * @return void
     */
    private function formatPasswordData(string $key) {

        if (Str::contains(Str::lower($key), 'password')) {

            if ($this->isUpdate) {

                if (is_null($this->request->get($key))) {

                    $this->request->remove($key);
                    $this->request->remove($key . '_confirmation');
                }
            }
        }
    }
}
