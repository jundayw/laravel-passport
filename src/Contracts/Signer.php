<?php

namespace Jundayw\Passport\Contracts;

interface Signer
{
    public function sign(string $algo, array $data, string $secret): string;

    public function verify(string $algo, array $data, string $sign, string $secret): bool;
}
