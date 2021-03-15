<?php

namespace MediactiveDigital\MedKit\Traits;

use Str;
use Arr;

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
     * @var array $requestAttributes
     */
    private $requestAttributes;

    /** 
     * @var array $ignoredNullFields
     */
    private $ignoredNullFields;

    /** 
     * @var string $tableNameSingular
     */
    private $tableNameSingular;

    /** 
     * @var string $translationForm
     */
    private $translationForm;

    /** 
     * @var array $customDatas
     */
    private $customDatas = [];

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
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {

        return $this->requestAttributes;
    }

    /**
     * Get fields removed from request when nullable (update).
     *
     * @return array
     */
    public function ignored() {

        return $this->ignoredNullFields;
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
     * Pass custom data to the Request instance.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return object
     */
    public function customData(string $key, $value) {

        $customDatas = $this->getCustomDatas();

        $customDatas[$key] = $value;

        $this->setCustomDatas($customDatas);

        return $this;
    }

    /**
     * Pass custom datas to the Request instance.
     *
     * @param array $customDatas
     *
     * @return object
     */
    public function customDatas(array $customDatas) {

        $this->setCustomDatas($customDatas);

        return $this;
    }

    /**
     * Get custom data from the Request instance.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCustomData(string $key) {

        return $this->customDatas[$key] ?? null;
    }

    /**
     * Get custom datas from the Request instance.
     *
     * @return array
     */
    public function getCustomDatas(): array {

        return $this->customDatas;
    }

    /**
     * Remove custom data from the Request instance.
     *
     * @param string $key
     *
     * @return object
     */
    public function removeCustomData(string $key) {

        $customDatas = $this->getCustomDatas();

        unset($customDatas[$key]);

        $this->setCustomDatas($customDatas);

        return $this;
    }

    /**
     * Remove custom datas from the Request instance.
     *
     * @return object
     */
    public function removeCustomDatas(): array {

        $this->setCustomDatas([]); 

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

        $request->tableNameSingular = $this->tableNameSingular;
        $request->translationForm = $this->translationForm;
        
        $this->modelId = $this->route($this->tableNameSingular);
        
        if (is_object($this->modelId)) {
            
            $this->modelId = $this->modelId->id;
        } 
        
        $this->setRules();
        $this->setMessages();
        $this->setAttributes();
        $this->setIgnored();

        $this->formatDatas();
    }

    /**
     * Set custom datas for the Request instance.
     *
     * @param array $customDatas
     *
     * @return void
     */
    protected function setCustomDatas(array $customDatas) {

        $this->customDatas = Arr::dot($customDatas);
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

            if (isset($this->requestRules[$key]) && in_array($key, $this->ignored()) && in_array('nullable', $this->requestRules[$key]) && is_null($value)) {

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
     * Set custom attributes for validator errors.
     *
     * @return void
     */
    private function setAttributes() {

        $this->requestAttributes = [];
    }

    /**
     * Set fields removed from request when nullable (update).
     *
     * @return void
     */
    private function setIgnored() {

        $this->ignoredNullFields = [
            'password'
        ];
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
