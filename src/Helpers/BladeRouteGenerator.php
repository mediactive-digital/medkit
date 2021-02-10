<?php

namespace MediactiveDigital\MedKit\Helpers;

use Tightenco\Ziggy\BladeRouteGenerator as ZiggyBladeRouteGenerator;
use Tightenco\Ziggy\Ziggy;

class BladeRouteGenerator extends ZiggyBladeRouteGenerator
{

    public static $generated;

    public function generate($group = false, $nonce = false)
    {

        $payload = new Ziggy($group, env('APP_URL'));
        $nonce = $nonce ? ' nonce="' . $nonce . '"' : '';

        if (static::$generated) {
            return $this->generateMergeJavascript(json_encode($payload->toArray()['routes']), $nonce);
        }

        $routeFunction = $this->getRouteFunction();

        static::$generated = true;

        return <<<HTML

    const Ziggy = {$payload->toJson()};
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
