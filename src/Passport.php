<?php

namespace Jundayw\Passport;

use BadMethodCallException;
use Jundayw\Passport\Support\HasArrayable;

class Passport implements Contracts\Passport
{
    use HasArrayable;

    /**
     * The passport data grouped by request section.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $data = [];

    /**
     * The normalized passport parameters used for signature generation.
     *
     * @var array<string, mixed>
     */
    protected array $parameters = [];

    /**
     * Create a new passport instance.
     *
     * @param string             $signatureKey The parameter name used to store the signature.
     * @param array<int, string> $params       The request parameter names included in the signature.
     * @param string|null        $prefix       The prefix used when converting parameter names to HTTP headers.
     */
    public function __construct(
        protected readonly string $signatureKey = 'signature',
        protected array $params = [],
        protected readonly string|null $prefix = 'x',
    ) {
        $params = $params ?: [
            'app_id',
            'action',
            'type',
            'charset',
            'format',
            'method',
            'version',
            'timestamp',
            'nonce',
        ];
        $this->parameters($this->params = [...$params, $signatureKey]);
    }

    /**
     * @inheritdoc
     *
     * @return static
     */
    public static function make(
        string $signatureKey = 'signature',
        array $params = [],
        string|null $prefix = 'x',
    ): static {
        return new static($signatureKey, $params, $prefix);
    }

    /**
     * Resolve the passport manager from the service container.
     *
     * @return Contracts\Manager
     */
    protected function getManager(): Contracts\Manager
    {
        return app(Contracts\Manager::class);
    }

    /**
     * Resolve the passport model from the service container.
     *
     * @return Contracts\Model\Passport
     */
    protected function getPassport(): Contracts\Model\Passport
    {
        return app(Contracts\Model\Passport::class);
    }

    /**
     * @inheritdoc
     *
     * @return bool True if the signature is valid or verification is bypassed, false otherwise
     */
    public function verify(string $key, string $algo, string $driver = 'hash_hmac'): bool
    {
        if (config('passport.enabled', true) === false || config('passport.ignore.request', false) === true) {
            return true;
        }

        if (is_null($signatureValue = $this->getSignatureValue())) {
            return false;
        }

        return $this->getManager()->driver($driver)->verify(
            $algo,
            $this->withoutSignature(),
            $this->getPassport()->getSecret($key),
            $signatureValue
        );
    }

    /**
     * @inheritdoc
     *
     * @return string The generated signature
     */
    public function signature(string $key, string $algo, string $driver = 'hash_hmac'): string
    {
        return $this->getManager()->driver($driver)->sign(
            $algo,
            $this->withoutSignature(),
            $this->getPassport()->getSecret($key)
        );
    }

    /**
     * @inheritdoc
     *
     * @return static Returns the current instance for method chaining
     */
    public function withSignature(string $key, string $algo, string $driver = 'hash_hmac'): static
    {
        if (config('passport.enabled', true) && config('passport.ignore.response', false) === false) {
            $response   = [
                $this->signatureKey => $this->signature($key, $algo, $driver),
            ];
            $this->data = array_map(function (array $data) use ($response) {
                return array_merge($data, $response);
            }, $this->toArray());
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return string|null The signature value if found, otherwise null
     */
    public function getSignatureValue(): string|null
    {
        return $this->getParameter($this->signatureKey);
    }

    /**
     * @inheritdoc
     *
     * @return array The modified data array with the signature key excluded
     */
    public function withoutSignature(): array
    {
        return array_map(function (array $data) {
            return array_filter($data, fn(string $key) => match (true) {
                $key == $this->signatureKey => false,
                $key == $this->toHeaderKey($this->signatureKey) => false,
                default => true,
            }, ARRAY_FILTER_USE_KEY);
        }, $this->toArray());
    }

    /**
     * Dynamically proxy method calls to the passport manager or its driver.
     *
     * @param string            $method    The method name.
     * @param array<int, mixed> $arguments The method arguments.
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call(string $method, array $arguments)
    {
        if (method_exists($this->getManager(), $method) || method_exists($this->getManager()->driver(), $method)) {
            return call_user_func_array([$this->getManager(), $method], $arguments);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
