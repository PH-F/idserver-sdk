<?php

namespace Tests\Client\Support;

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use RuntimeException;
use Tests\Concerns\MockGuzzleClient;
use Tests\TestCase;
use Xingo\IDServer\Client\Support\JsonStream;

class JsonStreamTest extends TestCase
{
    use MockGuzzleClient;

    /** @test */
    function it_can_convert_a_guzzle_response_stream_into_json()
    {
        $response = $this->mockResponse(200, json_encode(['data' => 'pong']))
            ->enableJsonStream()
            ->client()
            ->get('http://api.example.com/api/ping');

        $this->assertEquals([
            'data' => 'pong'
        ], $response->getBody()->asJson());
    }

    /** @test */
    function it_will_throw_an_exception_if_invalid_json_is_tried_to_be_loaded()
    {
        $response = $this->mockResponse(200, 'invalid')
            ->enableJsonStream()
            ->client()
            ->get('http://api.example.com/api/ping');

        $this->expectException(RuntimeException::class);

        $response->getBody()->asJson();
    }

    /** @test */
    function it_will_not_return_null_if_the_response_is_empty()
    {
        $response = $this->mockResponse(204, json_encode([]))
            ->enableJsonStream()
            ->client()
            ->get('http://api.example.com');

        $this->assertEquals([], $response->getBody()->asJson());
    }

    /**
     * Enable the json stream on this client.
     *
     * @return $this
     */
    private function enableJsonStream()
    {
        $this->stack->push(Middleware::mapResponse(function (Response $response) {
            $jsonStream = new JsonStream($response->getBody());

            return $response->withBody($jsonStream);
        }));

        return $this;
    }
}