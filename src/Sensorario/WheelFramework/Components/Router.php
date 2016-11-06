<?php

namespace Sensorario\WheelFramework\Components;

class Router
{
    private $server;

    private $posts;

    private $vars;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->posts = $_POST;
        $this->vars = [];

        // $_GET = null;
        // $_POST = null;
        // $_SERVER = null;
    }

    public function getUri() 
    {
        $uriAsArray = explode('/', $this->server['REQUEST_URI']);

        if (is_numeric(end($uriAsArray))) {
            $this->vars['id'] = array_pop($uriAsArray);
            $uriAsArray[] = '{id}';
        }

        return join('/', $uriAsArray);
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function getUser()
    {
        return isset($this->server['PHP_AUTH_USER'])
            ? $this->server['PHP_AUTH_USER']
            : null;
    }

    public function getHost() 
    {
        return $this->server['HTTP_HOST'];
    }

    public function getRequestMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function getPosts()
    {
        return $this->posts;
    }

    public function getFullUrl()
    {
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}
