<?php

namespace Tests\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

trait MockResponse
{
    /**
     * @var \Xingo\IDServer\Client
     */
    protected $client;

    /**
     * @param int $status
     * @param array $body
     * @param array $headers
     */
    public function mockResponse(int $status, array $body, array $headers = [])
    {
        $response = new Response(
            $status, $headers, $this->prepareBody($body)
        );

        $mock = new MockHandler([$response]);
        $handler = HandlerStack::create($mock);

        $httpClient = new Client(['handler' => $handler]);
        app()->instance(Client::class, $httpClient);

        $this->client = app()->make(\Xingo\IDServer\Client::class);
    }

    /**
     * @param array $body
     * @return string
     */
    protected function prepareBody(array $body): string
    {
        if (!array_key_exists('data', $body)) {
            $body = ['data' => $body];
        }

        return json_encode($body);
    }
}

