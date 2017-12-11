<?php

namespace Xingo\IDServer\Client\Middleware;

use Psr\Http\Message\RequestInterface;

class JwtToken
{
    const AUTH_BEARER = 'Bearer %s';

    /**
     * Called when the middleware is handled by the client.
     *
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $token = app('idserver.manager')->getToken();

            if (!empty($token)) {
                $request = $request->withHeader(
                    'Authorization',
                    sprintf(self::AUTH_BEARER, $token)
                );
            }

            return $handler($request, $options);
        };
    }
}
