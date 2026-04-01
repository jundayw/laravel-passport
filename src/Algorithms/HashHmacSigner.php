<?php

namespace Jundayw\Passport\Algorithms;

use Jundayw\Passport\Contracts\Signer;

class HashHmacSigner implements Signer
{
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
        return array_reduce($data, function ($value, array $data) {
            ksort($data);
            return $value.json_encode($data, JSON_UNESCAPED_UNICODE);
        }, '');
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
        return hash_hmac($algo, $this->httpBuildQuery($data), $secret);
    }

    /**
     * Verify that a given signature matches the computed HMAC of the data.
     *
     * @param string $algo           The hashing algorithm (e.g., 'sha256')
     * @param array  $data           The original data that was signed
     * @param string $signatureValue The signature to verify
     * @param string $secret         The secret key used for HMAC generation
     *
     * @return bool True if the signature is valid, false otherwise
     */
    public function verify(string $algo, array $data, string $signatureValue, string $secret): bool
    {
        return $this->sign($algo, $data, $secret) === $signatureValue;
    }
}
