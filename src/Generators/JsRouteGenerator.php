<?php

namespace MediactiveDigital\MedKit\Generators;

use MediactiveDigital\MedKit\Traits\Reflection;

use Tightenco\Ziggy\Ziggy;

class JsRouteGenerator extends Ziggy {

    use Reflection;

    protected $port;

    public function __construct($group = null, string $url = null) {

        parent::__construct($group, $url);

        $this->url = 'window.location.origin';
        $this->port = 'window.location.port';
    }

    /**
     * Convert this Ziggy instance to an array.
     */
    public function toArray(): array {

        return [
            'url' => $this->url,
            'port' => $this->port,
            'defaults' => method_exists(app('url'), 'getDefaultParameters') ? app('url')->getDefaultParameters() : [],
            'routes' => $this->callReflectionMethod('applyFilters', $this->group)->toArray()
        ];
    }
}
