<?php

namespace MediactiveDigital\MedKit\Providers;

use Illuminate\Support\ServiceProvider;

use MediactiveDigital\MedKit\Macros\Query;

class MacroServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        Query::toRawSql();
    }
}
