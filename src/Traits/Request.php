<?php

namespace MediactiveDigital\MedKit\Traits;

use MediactiveDigital\MedKit\Helpers\FormatHelper;

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
     * @return array $formatedRules
     */
    public function rules() {

        $formatedRules = [];
        $primaryKeyName = $this->modelInstance->getKeyName();
        $tableNameSingular = Str::singular($this->modelInstance->getTable());

        foreach ($this->modelInstance::$rules as $key => $rule) {

            $formatedRules[$key] = preg_replace_callback('/\$this->([a-zA-Z0-9]+)/', function($matches) use (&$primaryKeyName, &$tableNameSingular) {

                $id = $this->route($tableNameSingular);

                return $matches[1] == $primaryKeyName ? ($id ? '"' . $id . '"' : 'NULL') : '"' . str_replace('"', '""', $this->{$matches[1]}) . '"';

            }, $rule);

            if ($this->isUpdate) {

                if (Str::contains(Str::lower($key), 'password')) {

                    $formatedRules[$key] = str_replace('required', 'nullable', $formatedRules[$key]);
                }
            }
        }
        
        return $formatedRules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() {

        foreach ($this->all() as $parameter => $value) {

            if ($this->isUpdate) {

                if (Str::contains(Str::lower($key), 'password') && !$value) {

                    $this->request->remove($parameter);
                    $this->request->remove($parameter . '_confirmation');
                }
            }
        }
    }
}
