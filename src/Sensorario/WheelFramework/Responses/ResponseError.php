<?php

namespace Sensorario\WheelFramework\Responses;

class ResponseError
{
    private $json;

    private function __construct(array $params)
    {
        $this->json = $params;
    }

    public static function fromException(\Exception $e)
    {
        return new self([
            'message' => $e->getMessage(),
        ]);
    }

    public function getOutput()
    {
        return json_encode(
            $this->json
        );
    }
}
