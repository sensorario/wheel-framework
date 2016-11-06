<?php

namespace Sensorario\WheelFramework\Components;

use Sensorario\Container\Container;
use Sensorario\WheelFramework\Components\ResponseFactory;
use Sensorario\WheelFramework\Responses\ResponseError;

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

    private function ensureRouteExists($routes, $uri)
    {
        if (!isset($routes[$uri])) {
            header("HTTP/1.0 404 Not Found"); die;
        }
    }

    public function run()
    {
        $this->factory = $this->container->get('factory');
        $this->router  = $this->container->get('router');

        $uri    = $this->router->getUri();
        $routes = $this->config->getConfig('routes');

        $this->ensureRouteExists($routes, $uri);

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
