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

        app()->instance(Client::class, $this->createClient($handler));

        $this->client = app()->make(\Xingo\IDServer\Client::class);
    }

    /**
     * @param HandlerStack $handler
     * @return Client
     */
    protected function createClient(HandlerStack $handler)
    {
        $headers = [];

        if (session()->has('jwt_token')) {
            $headers['Authorization'] = 'Bearer ' . session('jwt_token');
        }

        $headers['X-XINGO-Client-ID'] = config('idserver.store.client_id');
        $headers['X-XINGO-Secret-Key'] = config('idserver.store.secret_key');

        return new Client([
            'handler' => $handler,
            'headers' => $headers,
        ]);
    }
}

