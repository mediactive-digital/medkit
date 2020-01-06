<?php

namespace MediactiveDigital\MedKit\Helpers;

use Tightenco\Ziggy\BladeRouteGenerator as ZiggyBladeRouteGenerator;

class BladeRouteGenerator extends ZiggyBladeRouteGenerator {

    private static $generated;
    private $baseProtocol;
    private $baseDomain;
    private $basePort;
    private $baseUrl;

    public function generate($group = false) {

        $json = $this->getRoutePayload($group)->toJson();

        if (static::$generated) {

            return $this->generateMergeJavascript($json);
        }

        $this->prepareDomain();

        $parent = (new \ReflectionClass($this))->getParentClass();
        $getRouteFunction = $parent->getMethod('getRouteFunction');
        $getRouteFunction->setAccessible(true);
        $routeFunction = $getRouteFunction->invoke($this);

        $defaultParameters = method_exists(app('url'), 'getDefaultParameters') ? json_encode(app('url')->getDefaultParameters()) : '[]';

        static::$generated = true;

        return <<<EOT
var Ziggy = {
    namedRoutes: $json,
    baseProtocol: {$this->baseProtocol},
    baseDomain: {$this->baseDomain},
    basePort: {$this->basePort},
    defaultParameters: $defaultParameters
};
Ziggy.baseUrl = {$this->baseUrl};
$routeFunction
EOT;
    }

    private function generateMergeJavascript($json) {

        return <<<EOT
(function() {
    var routes = $json;
    for (var name in routes) Ziggy.namedRoutes[name] = routes[name]
})();
EOT;
    }

    private function prepareDomain() {

        $this->baseProtocol = 'window.location.protocol.slice(0, -1)';
        $this->baseDomain = 'window.location.hostname';
        $this->basePort = 'window.location.port';
        $this->baseUrl = 'Ziggy.baseProtocol + "://" + Ziggy.baseDomain + (Ziggy.basePort ? ":" + Ziggy.basePort : "") + "/"';
    }
}
