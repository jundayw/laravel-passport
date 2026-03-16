<?php

namespace Jundayw\Passport;

use Closure;
use Illuminate\Database\Eloquent\Model;

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

    public function reset(): static
    {
        $this->data = [];
        return $this;
    }

    public function getSecret(string $key): string
    {
        $passport = with(cache()->get($key), function ($passport) use ($key) {
            return $passport ?? $this->getSecretByKeyFromCache($key);
        });

        if (is_null($passport)) {
            throw new \RuntimeException('passport is unavailable');
        }
        if (strcasecmp($passport->state, 'disable') === 0) {
            throw new \RuntimeException('passport is disable');
        }

        return $passport->getAttribute('secret');
    }

    public function getSecretByKeyFromCache(string $key): ?Model
    {
        $ttl = fn($passport) => is_null($passport) ? config('passport.ttl.fallback') : config('passport.ttl.resolved');

        return tap(static::useModel()->where([
            'key' => $key,
        ])->first(), static function ($passport) use ($key, $ttl) {
            cache()->put($key, $passport, $ttl($passport));
        });
    }

    public function check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): bool
    {
        $value = $this->data[$signature] ?? null;

        if (is_null($value)) {
            return false;
        }

        $data = array_filter($this->data, static function (string $key) use ($signature) {
            return $key !== $signature;
        }, ARRAY_FILTER_USE_KEY);

        return $this->manager->driver($driver)->verify($algo, $data, $value, $this->getSecret($key));
    }

    public function signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): string
    {
        $data = array_filter($this->data, static function (string $key) use ($signature) {
            return $key !== $signature;
        }, ARRAY_FILTER_USE_KEY);

        return $this->manager->driver($driver)->sign($algo, $data, $this->getSecret($key));
    }

    public function payload(array $data = [], bool $reset = false): static
    {
        if ($reset) {
            $this->reset();
        }

        $this->data = array_merge_recursive($this->data, $data);

        return $this;
    }

    public function extend(string $driver, Closure $callback): Manager
    {
        return $this->manager->extend($driver, $callback);
    }
}
