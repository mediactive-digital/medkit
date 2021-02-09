<?php

namespace MediactiveDigital\MedKit\Helpers;

use Tightenco\Ziggy\BladeRouteGenerator as ZiggyBladeRouteGenerator;
use Tightenco\Ziggy\Ziggy;

class BladeRouteGenerator extends ZiggyBladeRouteGenerator
{

    public static $generated;
    private $baseProtocol;
    private $baseDomain;
    private $basePort;
    private $baseUrl;


    public function generate($group = false, $nonce = false)
    {


        $this->prepareDomain();
        $payload = new Ziggy($group);
        $nonce = $nonce ? ' nonce="' . $nonce . '"' : '';

        if (static::$generated) {
            return $this->generateMergeJavascript(json_encode($payload->toArray()['routes']), $nonce);
        }

        $routeFunction = $this->getRouteFunction();

        static::$generated = true;

        return <<<HTML

    const Ziggy = {$payload->toJson()};
    Ziggy.baseUrl = {$this->baseUrl};
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
      
        $ziggyDir =__DIR__."/../../../../tightenco/ziggy/";
       
        return $ziggyDir . 'dist/index.js';
    }

    private function getRouteFunction()
    {
        if (config()->get('ziggy.skip-route-function')) {
            return '';
        }

        return file_get_contents($this->getRouteFilePath());
    }



    
    private function prepareDomain() {

        $this->baseProtocol = 'window.location.protocol.slice(0, -1)';
        $this->baseDomain = 'window.location.hostname';
        $this->basePort = 'window.location.port';
        $this->baseUrl = 'Ziggy.baseProtocol + "://" + Ziggy.baseDomain + (Ziggy.basePort ? ":" + Ziggy.basePort : "") + "/"';
    }
}
