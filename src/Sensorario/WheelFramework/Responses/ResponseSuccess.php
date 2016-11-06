<?php

namespace Sensorario\WheelFramework\Responses;

class ResponseSuccess implements Response
{
    private $params;

    private function __construct($params)
    {
        $this->params = $params;
    }

    public static function fromContent($data)
    {
        return new self([
            'data' => $data,
        ]);
    }

    public function withLink($name, $url)
    {
        $this->params['_links'][$name] = $url;

        return new self([
            'data' => $this->params['data'],
            '_links' => $this->params['_links'],
        ]);
    }

    public function getOutput()
    {
        $response['data'] = $this->params['data'];

        if (isset($this->params['_links'])) {
            $response['_links'] = $this->params['_links'];
        }

        return json_encode($response);
    }
}
