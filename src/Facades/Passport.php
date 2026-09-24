<?php

namespace Jundayw\Passport\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Jundayw\Passport\Contracts\Manager;
use Jundayw\Passport\Contracts\Passport as PassportContract;
use Jundayw\Passport\Contracts\Signer;
use Jundayw\Passport\Passport as Factory;

/**
 * @method static Factory make(string $signatureKey = 'signature', array $params = [], string|null $prefix = 'x')
 * @method static bool verify(string $key, string $algo, string $driver = 'hash_hmac')
 * @method static string signature(string $key, string $algo, string $driver = 'hash_hmac')
 * @method static Factory withSignature(string $key, string $algo, string $driver = 'hash_hmac')
 * @method static string|null getSignatureValue()
 * @method static array withoutSignature()
 * @method static Factory parameters(array $parameters = [])
 * @method static mixed getParameter(string $key, mixed $default = null)
 * @method static array getParameters()
 * @method static Factory header(array $data = [])
 * @method static array getHeader()
 * @method static Factory query(array $data = [])
 * @method static array getQuery()
 * @method static Factory request(array $data = [])
 * @method static array getRequest()
 * @method static Factory response(array $data = [])
 * @method static array getResponse()
 * @method static array toArray()
 * @method static Signer driver(string $name = 'hash_hmac')
 * @method static Manager extend(string $driver, Closure $callback)
 * @method static Signer createHashHmacDriver()
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
