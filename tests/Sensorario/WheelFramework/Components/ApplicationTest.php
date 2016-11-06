<?php

namespace Sensorario\Tests\WheelFramework\Components;

use PHPUnit_Framework_TestCase;
use Sensorario\WheelFramework\Components\Application;
use Sensorario\WheelFramework\Responses\ResponseError;
use Sensorario\WheelFramework\Responses\ResponseSuccess;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this
            ->getMockBuilder('Sensorario\\Container\\Container')
            ->disableOriginalConstructor()
            ->setMethods([
                'setConfiguration',
                'get',
            ])
            ->getMock();

        $this->config = $this
            ->getMockBuilder('Sensorario\\WheelFramework\\Components\\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->noServices = [];
    }

    public function testCollaboratorsWhereInitializedAtCreation()
    {
        $this->config->expects($this->once())
            ->method('getConfig')
            ->with('services')
            ->will($this->returnValue($this->noServices));

        $this->container->expects($this->once())
            ->method('setConfiguration')
            ->with($this->noServices);

        $this->application = new Application(
            $this->config,
            $this->container
        );

        $this->assertEquals(
            $this->container,
            $this->application->getContainer()
        );
    }

    public function test()
    {
        $this->servicesWithRouter = [
            'router' => [
                'class' => 'Sensorario\\WheelFramework\\Components\\Router',
            ]
        ];

        $this->uri = '/foo';

        $this->routes = [
            $this->uri => [
                
            ]
        ];

        $this->config->expects($this->at(0))
            ->method('getConfig')
            ->with('services')
            ->will($this->returnValue($this->servicesWithRouter));
        $this->config->expects($this->at(1))
            ->method('getConfig')
            ->with('routes')
            ->will($this->returnValue($this->routes));

        $this->container->expects($this->at(0))
            ->method('setConfiguration')
            ->with($this->servicesWithRouter);

        $this->router = $this
            ->getMockBuilder('Sensorario\\WheelFramework\\Components\\Router')
            ->disableOriginalConstructor()
            ->getMock();
        $this->factory = $this
            ->getMockBuilder('Sensorario\\WheelFramework\\Components\\ResponseFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container->expects($this->at(1))
            ->method('get')
            ->with('factory')
            ->will($this->returnValue($this->factory));
        $this->container->expects($this->at(2))
            ->method('get')
            ->with('router')
            ->will($this->returnValue($this->router));

        $this->router->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($this->uri));

        $this->factory->expects($this->once())
            ->method('init')
            ->with(
                $this->config,
                $this->container,
                $this->routes[$this->uri]
            );
        $this->factory->expects($this->once())
            ->method('initController');
        $this->factory->expects($this->once())
            ->method('callAction')
            ->will($this->returnValue(
                $response = ResponseSuccess::fromContent('foo')
            ));

        $this->application = new Application(
            $this->config,
            $this->container
        );

        $return = $this->application->run();

        $this->assertEquals(
            $response,
            $return
        );
    }

    public function testInvaldUriReturn404()
    {
        $this->markTestIncomplete();
    }
}
