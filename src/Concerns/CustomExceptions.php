<?php

namespace Xingo\IDServer\Concerns;

use Psr\Http\Message\ResponseInterface;
use Xingo\IDServer\Exceptions\AuthorizationException;
use Xingo\IDServer\Exceptions\ForbiddenException;
use Xingo\IDServer\Exceptions\ServerException;
use Xingo\IDServer\Exceptions\ValidationException;

trait CustomExceptions
{
    /**
     * @param ResponseInterface $response
     * @throws ServerException
     */
    protected function checkServerError(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 500) {
            throw new ServerException;
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws ValidationException
     */
    protected function checkValidation(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 422) {
            $content = $response->getBody()->asJson();

            throw new ValidationException($content['errors']);
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws AuthorizationException
     */
    protected function checkAuthorization(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 401) {
            throw new AuthorizationException;
        }
    }

    /**
     * @param ResponseInterface $response
     * @throws ForbiddenException
     */
    protected function checkForbidden(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 403) {
            throw new ForbiddenException;
        }
    }
}
