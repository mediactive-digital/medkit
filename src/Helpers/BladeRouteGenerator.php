<?php

namespace MediactiveDigital\MedKit\Helpers;

use Tightenco\Ziggy\BladeRouteGenerator as ZiggyBladeRouteGenerator;

use MediactiveDigital\MedKit\Generators\JsRouteGenerator;

class BladeRouteGenerator extends ZiggyBladeRouteGenerator
{

    public static $generated;

    public function generate($group = false, $nonce = false)
    {

        $payload = new JsRouteGenerator($group);
        $nonce = $nonce ? ' nonce="' . $nonce . '"' : '';

        if (static::$generated) {
            return $this->generateMergeJavascript(json_encode($payload->toArray()['routes']), $nonce);
        }

        $json = $payload->jsonSerialize();
        $defaults = json_encode($json['defaults']);
        $routes = json_encode($json['routes']);
        $routeFunction = $this->getRouteFunction();

        static::$generated = true;

        return <<<HTML

    const Ziggy = {url: {$json['url']}, port: {$json['port']}, defaults: $defaults, routes: $routes};
    $routeFunction

HTML;
    }

    private function generateMergeJavascript($json, $nonce)
    {
        return <<<HTML

    (function () {
        const routes = {$json};

        for (let name in routes) {
            Ziggy.routes[name] = routes[name];
        }
    })();

HTML;
    }

    private function getRouteFilePath()
    {

        $ziggyDir = __DIR__ . "/../../../../tightenco/ziggy/";
        return $ziggyDir . 'dist/index.js';
    }

    private function getRouteFunction()
    {
        if (config()->get('ziggy.skip-route-function')) {
            return '';
        }
        return file_get_contents($this->getRouteFilePath());
    }
}
