<?php

namespace Xingo\IDServer\Exceptions;

use Exception;

class ValidationException extends Exception
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation Failed', 422);
    }
}
