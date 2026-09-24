<?php

namespace Jundayw\Passport\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Jundayw\Passport\Exceptions\InvalidPassportException;

interface Passport extends Arrayable
{
    /**
     * Create a new passport instance.
     *
     * @param string             $signatureKey The parameter name used to store the signature.
     * @param array<int, string> $params       The request parameter names included in the signature.
     * @param string|null        $prefix       The prefix used when converting parameter names to HTTP headers.
     *
     * @return static
     */
    public static function make(string $signatureKey = 'signature', array $params = [], string|null $prefix = 'x'): static;

    /**
     * Verify the request signature.
     *
     * Signature verification can be bypassed through the configured passport
     * request settings.
     *
     * @param string $key    The passport key used to resolve the signing secret.
     * @param string $algo   The hashing algorithm used for verification.
     * @param string $driver The signature driver used for verification.
     *
     * @return bool
     */
    public function verify(string $key, string $algo, string $driver = 'hash_hmac'): bool;

    /**
     * Generate a signature for the current passport data.
     *
     * @param string $key    The passport key used to resolve the signing secret.
     * @param string $algo   The hashing algorithm used to generate the signature.
     * @param string $driver The signature driver used for signing.
     *
     * @return string
     */
    public function signature(string $key, string $algo, string $driver = 'hash_hmac'): string;

    /**
     * Append the generated signature to the current passport data.
     *
     * The signature is appended to each data section represented by the
     * current passport instance.
     *
     * @param string $key    The passport key used to resolve the signing secret.
     * @param string $algo   The hashing algorithm used to generate the signature.
     * @param string $driver The signature driver used for signing.
     *
     * @return static
     */
    public function withSignature(string $key, string $algo, string $driver = 'hash_hmac'): static;

    /**
     * Retrieve the signature value from the current passport parameters.
     *
     * @return string|null
     */
    public function getSignatureValue(): string|null;

    /**
     * Get the passport data without the signature field.
     *
     * Both the parameter name and its corresponding HTTP header name
     * are excluded from the returned data.
     *
     * @return array<string, array<string, mixed>>
     */
    public function withoutSignature(): array;

    /**
     * Resolve configured passport parameters from the current request.
     *
     * The parameters are resolved from the request headers, request body,
     * or query string according to the first available signature location.
     *
     * @param array<int, string> $parameters The parameter names to resolve.
     *
     * @return static
     *
     * @throws InvalidPassportException When the signature cannot be found.
     */
    public function parameters(array $parameters = []): static;

    /**
     * Retrieve a passport parameter.
     *
     * @param string $key     The parameter name.
     * @param mixed  $default The value returned when the parameter does not exist.
     *
     * @return mixed
     */
    public function getParameter(string $key, mixed $default = null): mixed;

    /**
     * Retrieve all resolved passport parameters.
     *
     * @return array<string, mixed>
     */
    public function getParameters(): array;

    /**
     * Set the request headers used by the passport.
     *
     * @param array<string, mixed> $data The request header data.
     *
     * @return static
     */
    public function header(array $data = []): static;

    /**
     * Retrieve the current request headers.
     *
     * @return array<string, mixed>
     */
    public function getHeader(): array;

    /**
     * Set the query parameters used by the passport.
     *
     * @param array<string, mixed> $data The query parameter data.
     *
     * @return static
     */
    public function query(array $data = []): static;

    /**
     * Retrieve the current query parameters.
     *
     * @return array<string, mixed>
     */
    public function getQuery(): array;

    /**
     * Set the request payload used by the passport.
     *
     * @param array<string, mixed> $data The request payload.
     *
     * @return static
     */
    public function request(array $data = []): static;

    /**
     * Retrieve the current request payload.
     *
     * @return array<string, mixed>
     */
    public function getRequest(): array;

    /**
     * Set the response data used by the passport.
     *
     * @param array<string, mixed> $data The response data.
     *
     * @return static
     */
    public function response(array $data = []): static;

    /**
     * Retrieve the current response data.
     *
     * @return array<string, mixed>
     */
    public function getResponse(): array;
}
