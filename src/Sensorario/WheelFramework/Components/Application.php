<?php

namespace Sensorario\WheelFramework\Components;

use Sensorario\WheelEngine\Engine;
use Sensorario\WheelFramework\Components\ResponseFactory;
use Sensorario\Container\Container;

class Application
{
    private $config;

    private $engine;

    private $factory;

    private $container;

    public function __construct(
        Config $config,
        Container $container
    ) {
        $this->config = $config;
        $this->container = $container;

        $this->container->setConfiguration($this->config->getConfig('services'));

        $this->engine = $this->container->get('engine');
        $this->factory = $this->container->get('factory');
        $this->manager = $this->container->get('manager');
        $this->router = $this->container->get('router');

        $this->manager->setConfiguration($this->config);
        $this->manager->init();
    }

    public function run()
    {
        $routes = $this->config->getConfig('routes');

        $route = !isset($routes[$this->router->getUri()])
            ? $this->config->getConfig('default_route')
            : $routes[$this->router->getUri()];

        $this->factory->init(
            $this->config,
            $this->router,
            $this->engine,
            $this->manager,
            $route
        );
        $this->factory->initController();

        try {
            $response = $this->factory->callAction();
        } catch (\Exception $exception) {
            return json_encode([
                'message'=>$exception->getMessage(),
            ]);
        }

        /** @todo expose isErrorResponse */
        if ('Sensorario\WheelFramework\Responses\ResponseError' == get_class($response)) { 
            header('HTTP/1.0 404 Not Found');
            header('Content-type: application/json');
        }


        /** @var $response Magna\Responses\ResponseSuccess */
        return $response->getOutput();
    }
}
