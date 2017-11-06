<?php

namespace Xingo\IDServer\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        $message = $message ?: 'Forbidden';
        parent::__construct($message, 403);
    }
}
