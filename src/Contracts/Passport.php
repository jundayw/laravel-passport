<?php

namespace Jundayw\Passport\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Jundayw\Passport\Manager;

interface Passport
{
    public function getSecret(string $key): string;

    public function getSecretByKeyFromCache(string $key): ?Model;

    public function check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): bool;

    public function signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): string;

    public function withSignature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): static;

    public function payload(array $data = [], bool $mergeRecursive = false): static;

    public function getPayload(): array;

    public function extend(string $driver, Closure $callback): Manager;
}
