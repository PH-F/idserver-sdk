<?php

namespace Xingo\IDServer\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = '')
    {
        $message = $message ?: 'Not Found';
        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }
}
