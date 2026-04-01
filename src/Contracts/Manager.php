<?php

namespace Jundayw\Passport\Contracts;

use Closure;

interface Manager
{
    public function driver(string $name = 'hash_hmac'): Signer;

    public function createHashHmacDriver(): Signer;

    /**
     * Register a custom driver creator Closure.
     *
     * @param string  $driver
     * @param Closure $callback
     *
     * @return static
     */
    public function extend(string $driver, Closure $callback): static;
}
