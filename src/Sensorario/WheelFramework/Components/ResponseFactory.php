<?php

namespace Sensorario\WheelFramework\Components;

use Sensorario\WheelFramework\Responses\ResponseError;
use Sensorario\WheelFramework\Responses\ResponseSuccess;
use Sensorario\WheelFramework\Components\Router;
use Sensorario\WheelFramework\Components\Manager;

class ResponseFactory
{
    private $config;

    private $router;

    private $manager;

    private $controller;

    public function init(
        Config $config,
        Router $router,
        Manager $manager,
        array $route
    ) {
        $this->config = $config;
        $this->router = $router;
        $this->manager = $manager;
        $this->route = $route;
    }

    public function initController()
    {
        if (!class_exists($this->route['controller'])) {
            throw new \RuntimeException(
                'Oops!'
            );
        }

        $this->controller = (new $this->route['controller'](
            $this->config,
            $this->router,
            $this->manager
        ));
    }

    public function callAction()
    {
        try {
            $this->ensureMethodIsAllowed();
            $action = $this->route[$this->router->getRequestMethod()];

            if (in_array($this->router->getRequestMethod(), ['POST'])) {
                $resource = $action['resource'];
                $this->ensureRequestIsWellFormed($resource);
            }
        } catch (\Exception $e) {
            return ResponseError::fromException($e);
        }

        $method = $action['action'];
        return ResponseSuccess::fromContent(
            $this->controller->$method(),
            isset($action['render_as'])
                ? $action['render_as']
                : 'json'
        );
    }

    private function ensureMethodIsAllowed()
    {
        $httpVerb = $this->router->getRequestMethod();
        $routeIsNotAllowed = !isset($this->route[$httpVerb]);
        if ($routeIsNotAllowed) {
            throw new \RuntimeException(
                'Invalid request'
            );
        }
    }

    private function ensureRequestIsWellFormed($resourceName)
    {
        \Sensorario\Resources\Resource::box(
            $request = json_decode(
                file_get_contents('php://input'),
                true
            ),
            new \Sensorario\Resources\Configurator(
                $resourceName,
                new \Sensorario\Resources\Container(array(
                    'resources' => $this->config->getConfig('resources')
                ))
            )
        );
    }
}
