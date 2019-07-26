<?php

namespace MediactiveDigital\MedKit\Facades;

use Illuminate\Support\Facades\Facade;

class MedKit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'medkit';
    }
}
