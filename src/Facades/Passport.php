<?php

namespace Jundayw\Passport\Facades;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Jundayw\Passport\Manager;

/**
 * @method static string getSecret(string $key)
 * @method static null|Model getSecretByKeyFromCache(string $key)
 * @method static bool check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static string signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static \Jundayw\Passport\Passport withSignature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static \Jundayw\Passport\Passport payload(array $data = [], bool $mergeRecursive = false)
 * @method static array getPayload()
 * @method static Manager extend(string $driver, Closure $callback)
 *
 * @see \Jundayw\Passport\Passport
 * @see \Jundayw\Passport\Contracts\Passport
 */
class Passport extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Jundayw\Passport\Contracts\Passport::class;
    }
}
