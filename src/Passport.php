<?php

namespace Jundayw\Passport;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Jundayw\Passport\Exceptions\PassportDisabledException;
use Jundayw\Passport\Exceptions\PassportNotFoundException;

class Passport implements Contracts\Passport
{
    protected static string $model = \Jundayw\Passport\Model\Passport::class;

    public static function modelUsing(string $model): void
    {
        static::$model = $model;
    }

    public static function useModel(): Model
    {
        return app(static::$model);
    }

    protected array $data = [];

    public function __construct(
        protected Manager $manager,
    ) {
        //
    }

    /**
     * @param string $key
     *
     * @return string
     * @throws PassportNotFoundException
     * @throws PassportDisabledException
     */
    public function getSecret(string $key): string
    {
        $passport = cache($key) ?? $this->getSecretByKeyFromCache($key);

        if (is_null($passport)) {
            throw new PassportNotFoundException(
                sprintf('Passport not found for key: %s', $key)
            );
        }

        if (strcasecmp($passport->state, 'disable') === 0) {
            throw new PassportDisabledException(
                sprintf('Passport is disabled for key: %s', $key)
            );
        }

        return $passport->getAttribute('secret');
    }

    /**
     * @param string $key
     *
     * @return Model|null
     */
    public function getSecretByKeyFromCache(string $key): ?Model
    {
        $ttl = fn($passport) => is_null($passport) ? config('passport.ttl.fallback') : config('passport.ttl.resolved');

        return tap(static::useModel()->where([
            'key' => $key,
        ])->first(), static function ($passport) use ($key, $ttl) {
            cache()->put($key, $passport, $ttl($passport));
        });
    }

    /**
     * @param string $key
     * @param string $algo
     * @param string $signature
     * @param string $driver
     *
     * @return bool
     */
    public function check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): bool
    {
        if (config('passport.enabled', true) === false || config('passport.ignore.request', false) === true) {
            return true;
        }

        $value = $this->data[$signature] ?? null;

        if (is_null($value)) {
            return false;
        }

        $data = array_filter($this->getPayload(), static function (string $key) use ($signature) {
            return $key !== $signature;
        }, ARRAY_FILTER_USE_KEY);

        return $this->manager->driver($driver)->verify($algo, $data, $value, $this->getSecret($key));
    }

    /**
     * @param string $key
     * @param string $algo
     * @param string $signature
     * @param string $driver
     *
     * @return string
     */
    public function signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): string
    {
        $data = array_filter($this->getPayload(), static function (string $key) use ($signature) {
            return $key !== $signature;
        }, ARRAY_FILTER_USE_KEY);

        return $this->manager->driver($driver)->sign($algo, $data, $this->getSecret($key));
    }

    /**
     * @param string $key
     * @param string $algo
     * @param string $signature
     * @param string $driver
     *
     * @return static
     */
    public function withSignature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): static
    {
        if (config('passport.enabled', true) && config('passport.ignore.response', false) === false) {
            $this->data[$signature] = $this->signature($key, $algo, $signature, $driver);
        }

        return $this;
    }

    /**
     * @param array $data
     * @param bool  $mergeRecursive
     *
     * @return static
     */
    public function payload(array $data = [], bool $mergeRecursive = false): static
    {
        if ($mergeRecursive) {
            $this->data = array_merge_recursive($this->getPayload(), $data);
        } else {
            foreach ($data as $key => $value) {
                $this->data[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->data;
    }

    /**
     * @param string  $driver
     * @param Closure $callback
     *
     * @return Manager
     */
    public function extend(string $driver, Closure $callback): Manager
    {
        return $this->manager->extend($driver, $callback);
    }
}
