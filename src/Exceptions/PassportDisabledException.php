<?php

namespace Jundayw\Passport\Exceptions;

use Throwable;

class PassportDisabledException extends InvalidPassportException
{
    public function __construct(string $message = 'PassportDisabled', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
