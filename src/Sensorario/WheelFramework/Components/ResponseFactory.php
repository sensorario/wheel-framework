<?php

namespace Sensorario\WheelFramework\Components;

use Sensorario\Container\Container;
use Sensorario\WheelFramework\Components\Manager;
use Sensorario\WheelFramework\Components\Router;
use Sensorario\WheelFramework\Responses\ResponseError;
use Sensorario\WheelFramework\Responses\ResponseSuccess;

class ResponseFactory
{
    private $config;

    private $router;

    private $manager;

    private $controller;

    private $container;

    public function init(
        Config $config,
        Container $container,
        array $route
    ) {
        $this->container = $container;
        $this->route = $route;
        $this->config = $config;
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
            $this->container
        ));
    }

    public function callAction()
    {
        $router = $this->container->get('router');

        try {
            $this->ensureMethodIsAllowed();
            $action = $this->route[$router->getRequestMethod()];

            if (in_array($router->getRequestMethod(), ['POST'])) {
                $resource = $action['resource'];
                $this->ensureRequestIsWellFormed($resource);
            }
        } catch (\Exception $e) {
            return ResponseError::fromException($e);
        }

        $method = $action['action'];

        $response = ResponseSuccess::fromContent(
            $this->controller->$method(),
            isset($action['render_as'])
                ? $action['render_as']
                : 'json'
        );

        $response = $response->withLink('self', $router->getFullUrl());

        return $response;
    }

    private function ensureMethodIsAllowed()
    {
        $router = $this->container->get('router');
        $httpVerb = $router->getRequestMethod();
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
