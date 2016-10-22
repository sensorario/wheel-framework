<?php

namespace Sensorario\WheelFramework\Components;

use Sensorario\WheelEngine\Engine;
use Sensorario\WheelFramework\Components\Router;
use Sensorario\WheelFramework\Components\ResponseFactory;
use Sensorario\WheelFramework\Components\Manager;

class Application
{
    private $config;

    private $router;

    private $engine;

    private $manager;

    private $factory;

    public function __construct(
        Config $config,
        Router $router,
        Engine $engine,
        Manager $manager,
        ResponseFactory $factory
    ) {
        $this->config = $config;
        $this->router = $router;
        $this->engine = $engine;
        $this->manager = $manager;
        $this->factory = $factory;

        /** @todo createa an interface for all the collaborators that
         * will needs configuration or other kind of injections */
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
