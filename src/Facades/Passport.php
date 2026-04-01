<?php

namespace Jundayw\Passport\Facades;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Jundayw\Passport\Contracts\Manager;
use Jundayw\Passport\Contracts\Passport as PassportContract;
use Jundayw\Passport\Contracts\Signer;
use Jundayw\Passport\Passport as Factory;

/**
 * @method static string getSecret(string $key)
 * @method static Model|null getSecretByKeyFromCache(string $key)
 * @method static bool check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static string signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static Factory withSignature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac')
 * @method static Factory header(array $data = [])
 * @method static array|null getHeader(string $signature = 'signature')
 * @method static Factory query(array $data = [])
 * @method static array|null getQuery(string $signature = 'signature')
 * @method static Factory request(array $data = [])
 * @method static array|null getRequest(string $signature = 'signature')
 * @method static Factory response(array $data = [])
 * @method static array|null getResponse(string $signature = 'signature')
 * @method static string|null extractSignature(string $signature = 'signature')
 * @method static array withoutSignature(string $signature = 'signature')
 * @method static array toArray()
 * @method static Signer driver(string $name = 'hash_hmac')
 * @method static Manager extend(string $driver, Closure $callback)
 * @method static Signer createHashHmacDriver()
 * @method static string sign(string $algo, array $data, string $secret)
 * @method static bool verify(string $algo, array $data, string $signatureValue, string $secret)
 *
 * @see Factory
 * @see PassportContract
 */
class Passport extends Facade
{
    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return PassportContract::class;
    }
}
