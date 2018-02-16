<?php

namespace Xingo\IDServer\Concerns;

use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Exceptions;

trait CustomException
{
    /**
     * @param ResponseInterface $response
     * @throws Exceptions\AuthorizationException
     * @throws Exceptions\ForbiddenException
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServerException
     * @throws Exceptions\ThrottleException
     * @throws Exceptions\ValidationException
     */
    protected function throwsException(ResponseInterface $response)
    {
        switch ($response->getStatusCode()) {
            case 401:
                throw new Exceptions\AuthorizationException();
            case 403:
                throw new Exceptions\ForbiddenException();
            case 404:
                throw new Exceptions\NotFoundException();
            case 422:
                $content = $response->getBody()->asJson();
                throw new Exceptions\ValidationException($content['errors']);
            case 429:
                throw new Exceptions\ThrottleException();
            default:
                throw new Exceptions\ServerException();
        }
    }
}
