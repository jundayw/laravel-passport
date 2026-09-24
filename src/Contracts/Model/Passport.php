<?php

namespace Jundayw\Passport\Contracts\Model;

use Jundayw\Passport\Exceptions\PassportDisabledException;
use Jundayw\Passport\Exceptions\PassportNotFoundException;

interface Passport
{
    /**
     * Retrieve the secret associated with the given key.
     *
     * @param string $key The identifier of the passport entry
     *
     * @return string The secret value
     *
     * @throws PassportNotFoundException If no model is found for the given key
     * @throws PassportDisabledException If the found model has a 'disable' state
     */
    public function getSecret(string $key): string;
}
