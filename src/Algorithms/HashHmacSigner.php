<?php

namespace Jundayw\Passport\Algorithms;

use Jundayw\Passport\Contracts\Signer;
use Jundayw\Passport\Exceptions\InvalidPassportException;

class HashHmacSigner implements Signer
{
    /**
     * Normalize a value for use in the canonical query string.
     *
     * This method converts boolean values to their string representation while
     * preserving all other values without modification.
     *
     * @param mixed $value The value to be normalized
     *
     * @return mixed The normalized value
     */
    protected function normalizeValue(mixed $value): mixed
    {
        return match ($value) {
            true => 'true',
            false => 'false',
            default => $value,
        };
    }

    /**
     * Build a query string by sorting each sub-array and encoding it as JSON.
     *
     * This method flattens the given associative array by iterating over each
     * sub‑array, sorting its keys, and appending the JSON representation
     * (without escaping Unicode) to a single string.
     *
     * @param array $data An array of arrays to be processed
     *
     * @return string The concatenated JSON strings
     */
    protected function httpBuildQuery(array $data = []): string
    {
        ksort($data);

        $digits = [];
        // $data   = array_filter($data, fn($value) => match (true) {
        //     is_array($value) => count($value),
        //     $value === null => false,
        //     $value === '' => false,
        //     default => true
        // });

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->httpBuildQuery($value);
            }
            $value    = rawurlencode($this->normalizeValue($value));
            $digits[] = "{$key}={$value}";
        }

        return implode('&', $digits);
    }

    /**
     * Build a canonical query string from the given payloads.
     *
     * This method sorts the top-level payloads, removes empty payloads, converts
     * each remaining payload into a sorted and URL-encoded query string, and
     * concatenates all query strings using an ampersand.
     *
     * @param array $data An array of payloads to be canonicalized
     *
     * @return string The canonicalized query string
     */
    protected function canonicalize(array $data): string
    {
        $data = array_map(function (array $payload) {
            return $this->httpBuildQuery($payload);
        }, $data);
        $data = array_filter($data, fn(string $payload) => strlen($payload));

        return implode('&', array_values($data));
    }

    /**
     * Generate an HMAC signature for the provided data.
     *
     * The data is first normalized using {@see httpBuildQuery()} and then
     * signed with the given secret and algorithm.
     *
     * @param string $algo   The hashing algorithm (e.g., 'sha256')
     * @param array  $data   The input data to sign
     * @param string $secret The secret key used for HMAC generation
     *
     * @return string The computed HMAC signature
     */
    public function sign(string $algo, array $data, string $secret): string
    {
        $algo = strtolower(trim($algo));

        if (!in_array($algo, hash_hmac_algos(), true)) {
            throw new InvalidPassportException(
                sprintf('Unsupported HMAC algorithm: %s', $algo)
            );
        }
        // header('x-signature-raw: '.$this->canonicalize($data));
        return strtoupper(
            hash_hmac($algo, $this->canonicalize($data), $secret)
        );
    }

    /**
     * Verify that a given signature matches the computed HMAC of the data.
     *
     * @param string $algo           The hashing algorithm (e.g., 'sha256')
     * @param array  $data           The original data that was signed
     * @param string $secret         The secret key used for HMAC generation
     * @param string $signatureValue The signature to verify
     *
     * @return bool True if the signature is valid, false otherwise
     */
    public function verify(string $algo, array $data, string $secret, string $signatureValue): bool
    {
        return $this->sign($algo, $data, $secret) === $signatureValue;
    }
}
