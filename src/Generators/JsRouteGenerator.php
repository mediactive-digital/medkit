<?php

namespace MediactiveDigital\MedKit\Generators;

use Tightenco\Ziggy\Ziggy;

class JsRouteGenerator extends Ziggy {

    public function __construct($group = null) {

        parent::__construct($group);

        $this->url = 'window.location.origin';
        $this->port = 'window.location.port';
    }
}
