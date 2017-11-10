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
                    return $this->refreshToken($response);
                }
            );
        };
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function refreshToken(ResponseInterface $response)
    {
        $json = json_decode($response->getBody(), true);

        if (in_array('token_invalid', $json)) {
            /** @var Manager $manager */
            $manager = app()->make('idserver.manager');
            $manager->users->refreshToken();
        }

        return $response;
    }
}
