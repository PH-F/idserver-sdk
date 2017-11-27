<?php

namespace Xingo\IDServer\Concerns;

use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Exceptions;

trait CustomExceptions
{
    /**
     * @param ResponseInterface $response
     * @throws Exceptions\ServerException
     */
    protected function checkServerError(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 500) {
            throw new Exceptions\ServerException();
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws Exceptions\ValidationException
     */
    protected function checkValidation(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 422) {
            $content = $response->getBody()->asJson();

            throw new Exceptions\ValidationException($content['errors']);
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws Exceptions\AuthorizationException
     */
    protected function checkAuthorization(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 401) {
            throw new Exceptions\AuthorizationException();
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws Exceptions\ForbiddenException
     */
    protected function checkForbidden(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 403) {
            throw new Exceptions\ForbiddenException();
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws Exceptions\ThrottleException
     */
    protected function checkThrottle(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 403) {
            throw new Exceptions\ThrottleException();
        }
    }
}
