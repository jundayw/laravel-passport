<?php

namespace Jundayw\Passport\Contracts;

interface Signer
{
    /**
     * Generate an HMAC signature for the provided data.
     *
     * @param string $algo   The hashing algorithm (e.g., 'sha256')
     * @param array  $data   The input data to sign
     * @param string $secret The secret key used for HMAC generation
     *
     * @return string The computed HMAC signature
     */
    public function sign(string $algo, array $data, string $secret): string;

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
    public function verify(string $algo, array $data, string $signatureValue, string $secret): bool;
}
