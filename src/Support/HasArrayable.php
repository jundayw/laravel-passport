<?php

namespace Jundayw\Passport\Support;

use Illuminate\Support\Str;
use Jundayw\Passport\Exceptions\InvalidPassportException;

trait HasArrayable
{
    /**
     * Convert a parameter name to its HTTP header representation.
     *
     * For example, `app_id` becomes `X-App-Id` when the `x` prefix is used.
     *
     * @param string $key The parameter name.
     *
     * @return string
     */
    protected function toHeaderKey(string $key): string
    {
        $stringable = Str::of($key);
        $stringable = is_null($this->prefix) ? $stringable : $stringable->prepend("{$this->prefix}_");

        return $stringable->replace('_', '-')
            ->ucwords('-')
            ->toString();
    }

    /**
     * Convert an HTTP header name to its parameter representation.
     *
     * For example, `X-App-Id` becomes `app_id` when the `x` prefix is used.
     *
     * @param string $key The HTTP header name.
     *
     * @return string
     */
    protected function fromHeaderKey(string $key): string
    {
        return Str::of($key)
            ->replace('-', '_')
            ->lower()
            ->after("{$this->prefix}_")
            ->toString();
    }

    /**
     * @inheritdoc
     *
     * @return static
     */
    public function parameters(array $parameters = []): static
    {
        $method = match (true) {
            request()->headers->has($this->toHeaderKey($this->signatureKey)) => function (mixed $carry, mixed $item) {
                $key                  = $this->toHeaderKey($item);
                $value                = request()->headers->get($key);
                $current              = [$key => $value];
                $this->data['header'] = $this->data['header'] ?? [];
                $this->data['header'] += $current;
                return $carry + [$item => $value];
            },
            request()->request->has($this->signatureKey) => function (mixed $carry, mixed $item) {
                $current            = [$item => request()->request->get($item)];
                $this->data['data'] = $this->data['data'] ?? [];
                $this->data['data'] += $current;
                return $carry + $current;
            },
            request()->query->has($this->signatureKey) => function (mixed $carry, mixed $item) {
                $current              = [$item => request()->query->get($item)];
                $this->data['params'] = $this->data['params'] ?? [];
                $this->data['params'] += $current;
                return $carry + $current;
            },
            default => throw new InvalidPassportException('SignatureNotFound'),
        };

        $this->parameters = array_reduce($parameters, $method, []);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return mixed
     */
    public function getParameter(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     *
     * @return static Returns the current instance for method chaining
     */
    public function header(array $data = []): static
    {
        $this->data['header'] = $data;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getHeader(): array
    {
        return $this->data['header'] ?? [];
    }

    /**
     * @inheritdoc
     *
     * @return static Returns the current instance for method chaining
     */
    public function query(array $data = []): static
    {
        $this->data['params'] = $data;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->data['params'] ?? [];
    }

    /**
     * @inheritdoc
     *
     * @return static Returns the current instance for method chaining
     */
    public function request(array $data = []): static
    {
        $this->data['data'] = $data;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getRequest(): array
    {
        return $this->data['data'] ?? [];
    }

    /**
     * @inheritdoc
     *
     * @return static Returns the current instance for method chaining
     */
    public function response(array $data = []): static
    {
        $this->data = [
            'response' => $data,
        ];

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getResponse(): array
    {
        return $this->data['response'] ?? [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter(
            $this->data,
            fn(array $data) => count($data)
        );
    }
}
