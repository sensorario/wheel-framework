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
        $this->config = $config;
        $this->container = $container;

        $this->container->setConfiguration($this->config->getConfig('services'));

        $this->factory = $this->container->get('factory');
        $this->manager = $this->container->get('manager');
        $this->router = $this->container->get('router');

        $this->manager->setConfiguration($this->config);
        $this->manager->init();
    }

    public function run()
    {
        $routes = $this->config->getConfig('routes');

        try {
            if (!isset($routes[$this->router->getUri()])) {
                throw new \RuntimeException(
                    'Invalid Request'
                );
            }

            $route = $routes[$this->router->getUri()];

            $this->factory->init(
                $this->config,
                $this->container,
                $route
            );
            $this->factory->initController();
            $response = $this->factory->callAction();
        } catch (\Exception $exception) {
            return json_encode(array(
                'message'=>$exception->getMessage(),
            ));
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
