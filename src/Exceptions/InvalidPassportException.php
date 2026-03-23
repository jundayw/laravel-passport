<?php

namespace Jundayw\Passport\Exceptions;

use RuntimeException;
use Throwable;

class InvalidPassportException extends RuntimeException
{
    public function __construct(string $message = 'InvalidPassport', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
