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
     * @var string $primaryKeyName
     */
    private $primaryKeyName;

    /** 
     * @var string $tableNameSingular
     */
    private $tableNameSingular;

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

        $this->rules = $this->modelInstance::$rules;

        $this->formatRules();

        return $this->rules;
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

                $this->formatUniqueRule($key, $index);
                $this->formatRequiredRule($key, $index);
            }
        }
    }

    /**
     * Format unique rule.
     *
     * @param string $key
     * @param int $index
     * @return void
     */
    private function formatUniqueRule(string $key, int $index) {

        if (Str::startsWith($this->rules[$key][$index], 'unique:')) {

            $this->primaryKeyName = $this->primaryKeyName ?: $this->modelInstance->getKeyName();
            $this->tableNameSingular = $this->tableNameSingular ?: Str::singular($this->modelInstance->getTable());

            $this->rules[$key][$index] = preg_replace_callback('/\$this->([a-zA-Z0-9]+)/', function($matches) {

                return $matches[1] == $this->primaryKeyName ? (($id = $this->route($this->tableNameSingular)) ? '"' . $id . '"' : 'NULL') : '"' . str_replace('"', '""', $this->{$matches[1]}) . '"';

            }, $this->rules[$key][$index]);
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
