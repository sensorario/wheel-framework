<?php

namespace Sensorario\WheelFramework\Controllers;

use Sensorario\Container\Container;
use Sensorario\WheelFramework\Components\Config;

class Controller
{
    protected $config;

    protected $container;

    public function __construct(
        Config $config,
        Container $container
    ) {
        $this->config = $config;
        $this->container = $container;
    }
}
