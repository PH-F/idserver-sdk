<?php

namespace Xingo\IDServer\Client\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Client\Support\JsonStream;
use Xingo\IDServer\Manager;

class TokenExpired
{
    /**
     * @param  callable  $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                function (Response $response) use ($handler, $request, $options) {
                    if ($this->shouldRefreshToken($request, $response)) {
                        $handler = $this->refreshToken()->updateHandler();

                        return $handler($request, $options);
                    }

                    return $response;
                }
            );
        };
    }

    /**
     * Check if the jwt token need to be refreshed.
     *
     * @param  Request  $request
     * @param  ResponseInterface  $response
     *
     * @return bool
     */
    public function shouldRefreshToken($request, ResponseInterface $response): bool
    {
        if (! $response->getBody() instanceof JsonStream) {
            return false;
        }

        if ($this->isRefreshRequest($request)) {
            return false;
        }

        $json = $response->getBody()->asJson();
        $response->getBody()->rewind();

        return is_array($json) &&
               in_array('token_expired', $json);
    }

    /**
     * Refresh the JWT token.
     *
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

    /**
     * Check if the current request is already a refresh request.
     * If that's the case we don't refresh the token again..
     *
     * @param  Request  $request
     *
     * @return bool
     */
    protected function isRefreshRequest($request): bool
    {
        return Str::endsWith($request->getUri()->getPath(), 'auth/refresh');
    }
}
