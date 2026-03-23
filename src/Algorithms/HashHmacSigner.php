<?php

namespace Jundayw\Passport\Algorithms;

use Jundayw\Passport\Contracts\Signer;

class HashHmacSigner implements Signer
{
    protected function httpBuildQuery(array $data = []): string
    {
        $query = [];

        foreach ($data as $key => $value) {
            $value   = is_array($value) ? $this->httpBuildQuery($value) : rawurlencode(match ($value) {
                true => 'true',
                false => 'false',
                default => $value
            });
            $query[] = "{$key}={$value}";
        }

        return implode('&', $query);
    }

    public function sign(string $algo, array $data, string $secret): string
    {
        ksort($data);
        return hash_hmac($algo, $this->httpBuildQuery($data), $secret);
    }

    public function verify(string $algo, array $data, string $sign, string $secret): bool
    {
        return $this->sign($algo, $data, $secret) === $sign;
    }
}
