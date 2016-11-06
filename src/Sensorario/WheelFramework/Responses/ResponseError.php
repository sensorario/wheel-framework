<?php

namespace Sensorario\WheelFramework\Responses;

class ResponseError implements Response
{
    private $json;

    private function __construct(array $params)
    {
        $this->json = $params;
    }

    public function isError()
    {
        return true;
    }

    public static function withHttpStatusCode($statusCode) {
        return new self([
            'statusCode' => $statusCode,
        ]);
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
