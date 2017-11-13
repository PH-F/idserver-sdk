<?php

namespace Xingo\IDServer\Exceptions;

use Exception;

class ServerException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        $message = $message ?: 'Internal Server Error';
        parent::__construct($message, 500);
    }
}
