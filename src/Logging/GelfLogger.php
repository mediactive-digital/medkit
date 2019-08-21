<?php

namespace MediactiveDigital\MedKit\Logging;
use Monolog\Logger;
use Monolog\Handler\GelfHandler;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;

class GelfLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $handler = new GelfHandler(new Publisher(new UdpTransport($config['host'], $config['port'])));
        return new Logger('main', [$handler]);
    }
}
