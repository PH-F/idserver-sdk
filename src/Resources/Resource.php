<?php

namespace Xingo\IDServer\Resources;

use GuzzleHttp\Client;

abstract class Resource
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return array
     */
    protected function call(string $method, string $uri, array $params = []): array
    {
        $option = strtoupper($method) === 'GET' ?
            'query' : 'form_params';

        $response = $this->client->request($method, $uri, [
            $option => $params,
        ]);

        return json_decode(
            $response->getBody()->getContents(), true
        );
    }
}
