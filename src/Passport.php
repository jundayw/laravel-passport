<?php

namespace Jundayw\Passport;

use Illuminate\Database\Eloquent\Model;
use Jundayw\Passport\Exceptions\PassportDisabledException;
use Jundayw\Passport\Exceptions\PassportNotFoundException;

class Passport implements Contracts\Passport
{
    protected array $data = [];

    public function __construct(
        protected Contracts\Manager $manager,
        protected Contracts\Model\Passport $passport,
    ) {
        //
    }

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
    public function getSecret(string $key): string
    {
        $passport = cache($key) ?? $this->getSecretByKeyFromCache($key);

        if (is_null($passport)) {
            throw new PassportNotFoundException(
                sprintf('Model not found for key: %s', $key)
            );
        }

        if (strcasecmp($passport->state, 'disable') === 0) {
            throw new PassportDisabledException(
                sprintf('Model is disabled for key: %s', $key)
            );
        }

        return $passport->getAttribute('secret');
    }

    /**
     * Fetch the passport model from cache, or from the database and store it in the cache.
     *
     * @param string $key The identifier to look up
     *
     * @return Model|null The model instance if found, otherwise null
     */
    public function getSecretByKeyFromCache(string $key): ?Model
    {
        $ttl = fn($passport) => is_null($passport) ? config('passport.ttl.fallback') : config('passport.ttl.resolved');

        return tap($this->passport->where([
            'key' => $key,
        ])->first(), static function ($passport) use ($key, $ttl) {
            cache()->put($key, $passport, $ttl($passport));
        });
    }

    /**
     * Verify whether the provided signature is valid for the given key and algorithm.
     *
     * @param string $key       The passport key
     * @param string $algo      The hashing algorithm to use (e.g., 'sha256')
     * @param string $signature The array key where the signature is located (default: 'signature')
     * @param string $driver    The verification driver (default: 'hash_hmac')
     *
     * @return bool True if the signature is valid or verification is bypassed, false otherwise
     */
    public function check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): bool
    {
        if (config('passport.enabled', true) === false || config('passport.ignore.request', false) === true) {
            return true;
        }

        if (is_null($signatureValue = $this->extractSignature($signature))) {
            return false;
        }

        return $this->manager->driver($driver)->verify($algo, $this->withoutSignature($signature), $signatureValue, $this->getSecret($key));
    }

    /**
     * Generate a signature for the request data using the given key and algorithm.
     *
     * @param string $key       The passport key
     * @param string $algo      The hashing algorithm to use
     * @param string $signature The array key that will hold the signature (default: 'signature')
     * @param string $driver    The signing driver (default: 'hash_hmac')
     *
     * @return string The generated signature
     */
    public function signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): string
    {
        return $this->manager->driver($driver)->sign($algo, $this->withoutSignature($signature), $this->getSecret($key));
    }

    /**
     * Append a signature to the response data if the feature is enabled.
     *
     * @param string $key       The passport key
     * @param string $algo      The hashing algorithm to use
     * @param string $signature The array key where the signature will be stored (default: 'signature')
     * @param string $driver    The signing driver (default: 'hash_hmac')
     *
     * @return static Returns the current instance for method chaining
     */
    public function withSignature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): static
    {
        if (config('passport.enabled', true) && config('passport.ignore.response', false) === false) {
            $response   = [
                $signature => $this->signature($key, $algo, $signature, $driver),
            ];
            $this->data = array_map(function (array $data) use ($response) {
                return array_merge($data, $response);
            }, $this->data);
        }

        return $this;
    }

    /**
     * Merge additional header data with existing headers.
     *
     * @param array $data The header data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function header(array $data = []): static
    {
        $this->data['header'] = array_replace_recursive($this->getHeader() ?? [], $data);

        return $this;
    }

    /**
     * Retrieve the current header data.
     *
     * @return array|null The header array, or null if not set
     */
    public function getHeader(): ?array
    {
        return $this->data['header'] ?? null;
    }

    /**
     * Merge additional query data with existing query parameters.
     *
     * @param array $data The query data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function query(array $data = []): static
    {
        $this->data['query'] = array_replace_recursive($this->getQuery() ?? [], $data);

        return $this;
    }

    /**
     * Retrieve the current query data.
     *
     * @return array|null The query array, or null if not set
     */
    public function getQuery(): ?array
    {
        return $this->data['query'] ?? null;
    }

    /**
     * Merge additional request data with existing request payload.
     *
     * @param array $data The request data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function request(array $data = []): static
    {
        $this->data['request'] = array_replace_recursive($this->getRequest() ?? [], $data);

        return $this;
    }

    /**
     * Retrieve the current request data.
     *
     * @return array|null The request array, or null if not set
     */
    public function getRequest(): ?array
    {
        return $this->data['request'] ?? null;
    }

    /**
     * Merge additional response data with existing response payload.
     *
     * @param array $data The response data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function response(array $data = []): static
    {
        $this->data['response'] = array_replace_recursive($this->getResponse() ?? [], $data);

        return $this;
    }

    /**
     * Retrieve the current response data.
     *
     * @return array|null The response array, or null if not set
     */
    public function getResponse(): ?array
    {
        return $this->data['response'] ?? null;
    }

    /**
     * Convert the stored data to a sorted array, filtering out empty sub‑arrays.
     *
     * @return array The processed data array
     */
    public function toArray(): array
    {
        $data = $this->data;
        ksort($data);
        return array_filter($data, fn(array $data) => count($data));
    }

    /**
     * Find the first occurrence of the signature value across all data sections.
     *
     * @param string $signature The key to look for (default: 'signature')
     *
     * @return string|null The signature value if found, otherwise null
     */
    public function extractSignature(string $signature = 'signature'): ?string
    {
        return array_reduce($this->toArray(), function ($value, array $data) use ($signature) {
            return $value ?? $data[$signature] ?? null;
        });
    }

    /**
     * Remove the signature key from every data section.
     *
     * @param string $signature The key to remove (default: 'signature')
     *
     * @return array The modified data array with the signature key excluded
     */
    public function withoutSignature(string $signature = 'signature'): array
    {
        return array_map(function (array $data) use ($signature) {
            return array_filter($data, fn(string $key) => !($key == $signature), ARRAY_FILTER_USE_KEY);
        }, $this->toArray());
    }

    public function __call(string $method, array $arguments)
    {
        if (method_exists($this->manager, $method) || method_exists($this->manager->driver(), $method)) {
            return call_user_func_array([$this->manager, $method], $arguments);
        }

        throw new \BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
