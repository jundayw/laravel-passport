<?php

namespace Jundayw\Passport\Facades;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Jundayw\Passport\Manager;

/**
 * @method static bool check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static string signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static \Jundayw\Passport\Passport reset()
 * @method static \Jundayw\Passport\Passport payload(array $data = [], bool $reset = false)
 * @method static Manager extend(string $driver, Closure $callback)
 * @method static string getSecret(string $key)
 * @method static null|Model getSecretByKeyFromCache(string $key)
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
