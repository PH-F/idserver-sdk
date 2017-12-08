<?php

namespace Xingo\IDServer\Client\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Manager;

class TokenExpired
{
    /**
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                function (Response $response) use ($handler, $request, $options) {
                    if ($this->shouldRefreshToken($response)) {
                        $handler = $this->refreshToken()->updateHandler();

                        return $handler($request, $options);
                    }

                    return $response;
                }
            );
        };
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    public function shouldRefreshToken(ResponseInterface $response): bool
    {
        $json = $response->getBody()->asJson();
        $response->getBody()->rewind();

        return is_array($json) &&
            in_array('token_expired', $json);
    }

    /**
     * @return $this
     */
    private function refreshToken()
    {
        /** @var Manager $manager */
        $manager = app()->make('idserver.manager');
        $manager->users->refreshToken();

        return $this;
    }

    /**
     * @return HandlerStack
     */
    private function updateHandler(): HandlerStack
    {
        /** @var Client $client */
        $client = app('idserver.client');

        /** @var HandlerStack $handler */
        $handler = $client->getConfig('handler');
        $handler->push(new JwtToken());

        return $handler;
    }
}
