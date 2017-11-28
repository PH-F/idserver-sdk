<?php

namespace Xingo\IDServer\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ThrottleException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        $message = $message ?: 'Too Many Requests';
        parent::__construct($message, Response::HTTP_TOO_MANY_REQUESTS);
    }
}
