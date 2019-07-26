<?php

namespace mediactive-digital\medkit\Facades;

use Illuminate\Support\Facades\Facade;

class medkit extends Facade
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
