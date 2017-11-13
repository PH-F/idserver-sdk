<?php

namespace Xingo\IDServer\Client\Middleware;

use Closure;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
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
            $response = $handler($request, $options);

            if ($this->tokenInvalid($response)) {
                $this->refreshToken();
                $response = $handler($request, $options);
            }

            return $response;
        };
    }

    /**
     * @param Response $response
     * @return bool
     */
    private function tokenInvalid(Response $response): bool
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
