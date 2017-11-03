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
            $status, $headers, json_encode($body)
        );

        $mock = new MockHandler([$response]);
        $handler = HandlerStack::create($mock);

        $httpClient = new Client(['handler' => $handler]);
        app()->instance(Client::class, $httpClient);

        $this->client = app()->make(\Xingo\IDServer\Client::class);
    }
}

