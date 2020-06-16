<?php

namespace MediactiveDigital\MedKit\Traits;

use Str;

trait Request {

    /** 
     * @var int $modelId
     */
    private $modelId;

    /** 
     * @var array $requestRules
     */
    private $requestRules;

    /** 
     * @var array $requestMessages
     */
    private $requestMessages;

    /** 
     * @var string $tableNameSingular
     */
    private $tableNameSingular;

    /** 
     * @var string $translationForm
     */
    private $translationForm;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return $this->requestRules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {

        return $this->requestMessages;
    }

    /**
     * Prepare the data for manual validation.
     *
     * @return object
     */
    public function validation() {

        $this->prepareForValidation();

        return $this;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() {

        $request = request();

        $this->setTableNameSingular();
        $this->setTranslationForm();

        $request->translationForm = $this->translationForm;
        
        $this->modelId = $this->route($this->tableNameSingular);
        
        if (is_object($this->modelId)) {
            
            $this->modelId = $this->modelId->id;
        } 
        
        $this->setRules();
        $this->setMessages();

        $this->formatDatas();
    }

    /**
     * Format request datas.
     *
     * @return void
     */
    private function formatDatas() {

        foreach ($this->all() as $key => $value) {

            $this->formatNullableData($key, $value);
        }
    }

    /**
     * Format nullable request data.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function formatNullableData(string $key, $value) {

        if ($this->modelId > 0) {

            if (isset($this->requestRules[$key]) && in_array('nullable', $this->requestRules[$key]) && is_null($value)) {

                $this->request->remove($key);

                if (in_array('confirmed', $this->requestRules[$key])) {

                    $this->request->remove($key . '_confirmation');
                }
            }
        }
    }

    /** 
     * Set rule.
     *
     * @param string $createRule
     * @param string $updateRule
     * @return string
     */
    private function setRule(string $createRule, string $updateRule = ''): string {

        return $this->modelId > 0 ? ($updateRule !== '' ? $updateRule : $createRule) : $createRule;
    }

    /**
     * Set the validation rules that apply to the request.
     *
     * @return void
     */
    private function setRules() {

        $this->requestRules = [];
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return void
     */
    private function setMessages() {

        $this->requestMessages = [];
    }

    /**
     * Set the singular table name to retrieve the model.
     *
     * @return void
     */
    private function setTableNameSingular() {

        $this->tableNameSingular = '';
    }

    /**
     * Set the form class to retrieve label translations.
     *
     * @return void
     */
    private function setTranslationForm() {

        $this->translationForm = '';
    }
}
