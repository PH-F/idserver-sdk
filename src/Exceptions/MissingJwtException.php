<?php

namespace Xingo\IDServer\Exceptions;

use Exception;

class MissingJwtException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        $message = $message ?: 'Missing JWT in the session';
        parent::__construct($message, 401);
    }
}
