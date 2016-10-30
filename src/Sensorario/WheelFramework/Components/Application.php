<?php

namespace Sensorario\WheelFramework\Components;

use Sensorario\WheelFramework\Components\ResponseFactory;
use Sensorario\Container\Container;

class Application
{
    private $config;

    private $factory;

    private $container;

    public function __construct(
        Config $config,
        Container $container
    ) {
        $services = $config->getConfig('services');

        $this->container = $container;
        $this->container->setConfiguration($services);

        $this->config = $config;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function run()
    {
        $this->factory = $this->container->get('factory');
        $this->router  = $this->container->get('router');

        $uri    = $this->router->getUri();
        $routes = $this->config->getConfig('routes');
        $route  = $routes[$uri];

        $this->factory->init(
            $this->config,
            $this->container,
            $route
        );

        $this->factory->initController();

        return $this->factory->callAction();
    }
}
