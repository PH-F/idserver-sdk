<?php

namespace Xingo\IDServer\Client\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Manager;

class InvalidToken
{
    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                function (ResponseInterface $response) {
                    if ($this->tokenInvalid($response)) {
                        $this->refreshToken();
                    }

                    return $response;
                }
            );
        };
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    private function tokenInvalid(ResponseInterface $response): bool
    {
        $json = $response->getBody()->asJson();

        return array_key_exists('token_invalid', $json);
    }

    /**
     * @return void
     */
    private function refreshToken()
    {
        /** @var Manager $manager */
        $manager = app()->make('idserver.manager');
        $manager->users->refreshToken();
    }
}
