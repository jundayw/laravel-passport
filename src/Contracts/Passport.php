<?php

namespace Jundayw\Passport\Contracts;

use Illuminate\Database\Eloquent\Model;
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

    /**
     * Fetch the passport model from cache, or from the database and store it in the cache.
     *
     * @param string $key The identifier to look up
     *
     * @return Model|null The model instance if found, otherwise null
     */
    public function getSecretByKeyFromCache(string $key): ?Model;

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
    public function check(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): bool;

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
    public function signature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): string;

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
    public function withSignature(string $key, string $algo, string $signature = 'signature', string $driver = 'hash_hmac'): static;

    /**
     * Merge additional header data with existing headers.
     *
     * @param array $data The header data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function header(array $data = []): static;

    /**
     * Retrieve the current header data.
     *
     * @return array|null The header array, or null if not set
     */
    public function getHeader(): ?array;

    /**
     * Merge additional query data with existing query parameters.
     *
     * @param array $data The query data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function query(array $data = []): static;

    /**
     * Retrieve the current query data.
     *
     * @return array|null The query array, or null if not set
     */
    public function getQuery(): ?array;

    /**
     * Merge additional request data with existing request payload.
     *
     * @param array $data The request data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function request(array $data = []): static;

    /**
     * Retrieve the current request data.
     *
     * @return array|null The request array, or null if not set
     */
    public function getRequest(): ?array;

    /**
     * Merge additional response data with existing response payload.
     *
     * @param array $data The response data to merge (overwrites existing keys recursively)
     *
     * @return static Returns the current instance for method chaining
     */
    public function response(array $data = []): static;

    /**
     * Retrieve the current response data.
     *
     * @return array|null The response array, or null if not set
     */
    public function getResponse(): ?array;

    /**
     * Convert the stored data to a sorted array, filtering out empty sub‑arrays.
     *
     * @return array The processed data array
     */
    public function toArray(): array;

    /**
     * Find the first occurrence of the signature value across all data sections.
     *
     * @param string $signature The key to look for (default: 'signature')
     *
     * @return string|null The signature value if found, otherwise null
     */
    public function extractSignature(string $signature = 'signature'): ?string;

    /**
     * Remove the signature key from every data section.
     *
     * @param string $signature The key to remove (default: 'signature')
     *
     * @return array The modified data array with the signature key excluded
     */
    public function withoutSignature(string $signature = 'signature'): array;
}
