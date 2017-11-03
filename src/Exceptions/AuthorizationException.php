<?php

namespace Xingo\IDServer\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        $message = $message ?: 'Unauthorized';
        parent::__construct($message, 401);
    }
}
