<?php

namespace Tests\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Xingo\IDServer\Manager;

trait MockResponse
{
    /**
     * @var \Xingo\IDServer\Manager
     */
    protected $manager;

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

        $client = new Client(['handler' => $handler]);
        app()->instance('idserver.client', $client);

        $this->manager = new Manager($client);
        app()->instance('idserver.manager', $this->manager);
    }
}

