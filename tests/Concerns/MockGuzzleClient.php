<?php

namespace Tests\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;

trait MockGuzzleClient
{
    /**
     * @var HandlerStack
     */
    protected $stack = null;

    /**
     * Mock the response of the guzzle client.
     *
     * @param int $status
     * @param null $body
     * @param array $headers
     * @return $this
     */
    protected function mockResponse($status = 200, $body = null, $headers = [])
    {
        $response = new Response($status, $headers, $body);

        $this->stack = HandlerStack::create(new MockHandler([$response]));

        return $this;
    }

    /**
     * Return a new guzzle client with a mock stack.
     *
     * @return Client
     */
    protected function client()
    {
        if (is_null($this->stack)) {
            $this->mockResponse();
        }

        return new Client(['handler' => $this->stack]);
    }
}