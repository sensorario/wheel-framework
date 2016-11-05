<?php

namespace Sensorario\WheelFramework\Responses;

class ResponseSuccess
{
    private $content;

    private function __construct($content)
    {
        $this->content = $content;
    }

    public static function fromContent($content)
    {
        return new self($content);
    }

    public function getOutput()
    {
        return json_encode([
            'data' => $this->content
        ]);
    }
}
