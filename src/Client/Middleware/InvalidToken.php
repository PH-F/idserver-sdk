<?php

namespace Xingo\IDServer\Client\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Manager;

class InvalidToken
{
    /**
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Response $response)
    {
        $json = $response->getBody()->asJson();

        if (in_array('token_invalid', $json)) {
            /** @var Manager $manager */
            $manager = app()->make('idserver.manager');
            $manager->users->refreshToken();
        }

        $response->getBody()->rewind();

        return $response;
    }
}
