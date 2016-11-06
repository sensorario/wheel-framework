<?php

namespace Sensorario\Tests\WheelFramework\Components;

use PHPUnit_Framework_TestCase;
use Sensorario\WheelFramework\Responses\ResponseSuccess;

class ResponseSuccessTest extends PHPUnit_Framework_TestCase
{
    public function testWrapsContentInDataElement()
    {
        $response = ResponseSuccess::fromContent('foo');
        $this->assertEquals(
            '{"data":"foo"}',
            $response->getOutput() 
        );
    }

    public function testCanContainLinks()
    {
        $response = ResponseSuccess::fromContent('foo');

        $response = $response->withLink(
            'foo',
            'bar'
        );

        $this->assertEquals(
            '{"data":"foo","_links":{"foo":"bar"}}',
            $response->getOutput() 
        );
    }
}
