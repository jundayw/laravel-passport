<?php

namespace Jundayw\Passport\Exceptions;

use Throwable;

class PassportNotFoundException extends InvalidPassportException
{
    public function __construct(string $message = 'PassportNotFound', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
