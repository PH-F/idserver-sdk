<?php

namespace Tests\Unit\Client\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Tests\Concerns\MockGuzzleClient;
use Tests\TestCase;
use Xingo\IDServer\Client\Middleware\JwtToken;

class JwtTokenTest extends TestCase
{
    use MockGuzzleClient;

    /** @test */
    public function it_will_automatically_attach_the_jwt_token_if_found_in_session()
    {
        app('idserver.manager')
            ->setToken('test-token');

        $this->setUpMockClientWithJwtTokenMiddleware(function (RequestInterface $request) {
            $this->assertTrue($request->hasHeader('Authorization'));
            $this->assertSame(
                sprintf(JwtToken::AUTH_BEARER, 'test-token'),
                $request->getHeader('Authorization')[0]
            );
        })
            ->get('http://api.example.com/api/ping');
    }

    /** @test */
    public function it_will_not_attach_an_auth_header_if_jwt_token_is_not_found_in_session()
    {
        $this->setUpMockClientWithJwtTokenMiddleware(function (RequestInterface $request) {
            $this->assertFalse($request->hasHeader('Authorization'));
        })
            ->get('http://api.example.com/api/ping');
    }

    /**
     * Create a mock client which will add the given assertion on the response.
     *
     * @param callable $assertion
     *
     * @return Client
     */
    protected function setUpMockClientWithJwtTokenMiddleware($assertion)
    {
        $this->stack = HandlerStack::create(new MockHandler([
            function (RequestInterface $request) use ($assertion) {
                $assertion($request);

                return new Response();
            },
        ]));

        $this->stack->push(new JwtToken());

        return $this->client();
    }
}
