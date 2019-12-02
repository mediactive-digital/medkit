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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $this->modelId = $this->route($this->tableNameSingular);

        $this->setRules();

        return $this->requestRules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {

        $this->setMessages();

        return $this->requestMessages;
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
}