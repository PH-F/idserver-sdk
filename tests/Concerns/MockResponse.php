<?php

namespace Tests\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Xingo\IDServer\Client\Middleware\InvalidToken;
use Xingo\IDServer\Client\Middleware\JwtToken;
use Xingo\IDServer\Client\Support\JsonStream;
use Xingo\IDServer\Manager;

trait MockResponse
{
    /**
     * @var \Xingo\IDServer\Manager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $history = [];

    /**
     * @var HandlerStack
     */
    private $stack;

    /**
     * @var MockHandler
     */
    private $handler;

    /**
     * @param int $status
     * @param array $body
     * @param array $headers
     */
    public function mockResponse(int $status = 200, array $body = [], array $headers = [])
    {
        $response = new Response(
            $status, $headers, json_encode($body)
        );

        $this->setUpClient($response);
    }

    /**
     * Create a mock handler for the given response.
     *
     * @param Response $response
     * @return HandlerStack
     */
    private function createHandler(Response $response): HandlerStack
    {
        if ($this->handler && $this->stack) {
            return $this->appendResponse($response);
        }

        $this->handler = new MockHandler([$response]);
        $stack = HandlerStack::create($this->handler);

        return $this->stack = $this->pushMiddleware($stack);
    }

    /**
     * @param Response $response
     */
    private function setUpClient(Response $response): void
    {
        $client = new Client(['handler' => $this->createHandler($response)]);
        app()->instance('idserver.client', $client);

        $this->manager = new Manager($client);
        app()->instance('idserver.manager', $this->manager);
    }

    /**
     * @param Response $response
     * @return HandlerStack
     */
    private function appendResponse(Response $response): HandlerStack
    {
        $this->handler->append($response);
        $this->stack->setHandler($this->handler);

        return $this->stack;
    }

    /**
     * @param HandlerStack $stack
     * @return HandlerStack
     */
    private function pushMiddleware(HandlerStack $stack): HandlerStack
    {
        // Make possible to test requests and responses later
        $stack->push(Middleware::history($this->history));

        $stack->push(new JwtToken(), 'jwt-token');
        $stack->push(new InvalidToken(), 'invalid-token');

        $stack->push(Middleware::mapResponse(function (Response $response) {
            $stream = new JsonStream($response->getBody());

            return $response->withBody($stream);
        }));

        return $stack;
    }
}
